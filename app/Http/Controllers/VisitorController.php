<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;
use AfricasTalking\SDK\AfricasTalking;

class VisitorController extends Controller
{
    /**
     * Display the public self-registration form for visitors.
     */
    public function create()
    {
        $hosts = User::where('role', 'host')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'display' => $user->name . " - " . ($user->department ?? 'Resident/Host')
                ];
            });
        
        return view('visitor.register', compact('hosts'));
    }

    /**
     * Dashboard Router: Redirects users to their specific dashboard based on role.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Guard Redirect
        if ($user->role === 'guard') {
            return redirect()->route('guard.dashboard');
        }

        // 2. Admin View: Shows Global Statistics
        if ($user->role === 'admin') {
            $visitors = Visitor::latest()->paginate(15);
            $viewTitle = "Global Activity Monitor";
            
            // FIXED: Standardized variable names to match dashboard UI cards
            $stats = [
                'total_visitors'   => Visitor::count(), 
                'pending_approval' => Visitor::where('status', 'Pending')->count(),
                'approved_today'   => Visitor::where('status', 'Approved')
                                            ->whereDate('updated_at', Carbon::today())
                                            ->count(),
                'active_hosts'     => User::where('role', 'host')->count(),
            ];
            
            return view('admin.dashboard', compact('visitors', 'viewTitle', 'stats'));
        } 

        // 3. Host View: Shows Statistics specific to this Host only
        $visitors = Visitor::where('host_name', $user->name)
                            ->latest()
                            ->paginate(15);
                            
        $viewTitle = "My Visitor Requests";

        // ADDED: Stats for the Host dashboard so they aren't empty
        $stats = [
            'total_visitors'   => Visitor::where('host_name', $user->name)->count(),
            'pending_approval' => Visitor::where('host_name', $user->name)->where('status', 'Pending')->count(),
            'approved_today'   => Visitor::where('host_name', $user->name)
                                        ->where('status', 'Approved')
                                        ->whereDate('updated_at', Carbon::today())
                                        ->count(),
            'active_hosts'     => null, // Hosts don't need to see global host counts
        ];

        return view('host.dashboard', compact('visitors', 'viewTitle', 'stats'));
    }

    /**
     * Show visitor details with a strict security check.
     */
    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);
        $user = Auth::user();

        // Security: Only Admins, Guards, or the assigned Host can view details
        $isHost = strtolower($visitor->host_name) === strtolower($user->name);

        if ($user->role !== 'admin' && $user->role !== 'guard' && !$isHost) {
            abort(403, 'Unauthorized access to this visitor record.');
        }

        return view('admin.visitor_details', compact('visitor'));
    }

    /*
    |--------------------------------------------------------------------------
    | GUARD DASHBOARD LOGIC
    |--------------------------------------------------------------------------
    */

    public function guardDashboard(Request $request)
    {
        $search = $request->input('search');

        $expected = Visitor::where('status', 'Approved')
            ->whereNull('checked_in_at')
            ->when($search, function($query) use ($search) {
                return $query->where('full_name', 'LIKE', "%{$search}%")
                             ->orWhere('id_number', 'LIKE', "%{$search}%");
            })->get();

        $inside = Visitor::where('status', 'Inside')
            ->when($search, function($query) use ($search) {
                return $query->where('full_name', 'LIKE', "%{$search}%");
            })->get();

        return view('guard.dashboard', compact('expected', 'inside'));
    }

    public function processCheckIn($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update([
            'checked_in_at' => Carbon::now(),
            'status' => 'Inside'
        ]);

        return back()->with('success', "{$visitor->full_name} has been cleared for entry.");
    }

    public function processCheckOut($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update([
            'checked_out_at' => Carbon::now(),
            'status' => 'Checked Out'
        ]);

        $this->notifyHostCheckout($visitor);

        return back()->with('success', "{$visitor->full_name} has checked out.");
    }

    public function guardCreate()
    {
        $hosts = User::where('role', 'host')->orderBy('name', 'asc')->get();
        return view('guard.register_visitor', compact('hosts'));
    }

    public function guardStore(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'nullable|string',
            'host_name' => 'required|string',
            'purpose'   => 'required|string',
            'id_number' => 'nullable|string',
        ]);

        $visitor = Visitor::create([
            'full_name'     => $validated['full_name'],
            'phone'         => $this->formatPhone($validated['phone'] ?? '0000000000'),
            'host_name'     => $validated['host_name'],
            'purpose'       => $validated['purpose'],
            'id_number'     => $validated['id_number'],
            'status'        => 'Inside',
            'checked_in_at' => Carbon::now(),
        ]);

        return redirect()->route('guard.dashboard')->with('success', 'Visitor registered and checked in.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS (Approve/Reject)
    |--------------------------------------------------------------------------
    */

    public function approve($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        if (Auth::user()->role !== 'admin' && strtolower($visitor->host_name) !== strtolower(Auth::user()->name)) {
            abort(403);
        }

        $visitor->update(['status' => 'Approved']);
        
        $badgeUrl = route('visitor.pass', ['id' => $visitor->id]);
        $this->notifyVisitor($visitor, "Approved! Show your digital badge at the gate: " . $badgeUrl);

        return back()->with('success', "Visitor approved.");
    }

    public function reject($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        if (Auth::user()->role !== 'admin' && strtolower($visitor->host_name) !== strtolower(Auth::user()->name)) {
            abort(403);
        }

        $visitor->update(['status' => 'Rejected']);
        $this->notifyVisitor($visitor, "Visit rejected by host.");

        return back()->with('success', "Visitor rejected.");
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC REGISTRATION
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => ['required', 'string', 'regex:/^(?:254|\+254|0)?(7|1)(?:[0-9]){8}$/'],
            'type'           => 'required|in:Adult,Minor',
            'host_name'      => 'required|string|max:255',
            'purpose'        => 'required|string',
            'purpose_other'  => 'required_if:purpose,Other|nullable|string',
            'accompanied'    => 'required_if:type,Minor|nullable',
            'id_number'      => 'required|string|max:50',
            'vehicle_reg'    => 'nullable|string|max:20',
            'signature_data' => 'required|string',
            'time_in'        => 'nullable',
        ]);

        try {
            $phone = $this->formatPhone($validated['phone']);
            $finalPurpose = ($validated['purpose'] === 'Other') ? $validated['purpose_other'] : $validated['purpose'];

            $visitor = Visitor::updateOrCreate(
                ['id_number' => $validated['id_number'], 'status' => 'Pending'],
                [
                    'full_name'     => $validated['full_name'],
                    'phone'         => $phone,
                    'type'          => $validated['type'],
                    'guardian_name' => $request->has('accompanied') ? 'Accompanied by Guardian' : null,
                    'host_name'     => $validated['host_name'],
                    'purpose'       => $finalPurpose,
                    'vehicle_reg'   => $validated['vehicle_reg'],
                    'signature'     => $validated['signature_data'],
                    'check_in'      => $validated['time_in'] ?? Carbon::now()->format('H:i'),
                ]
            );

            if ($visitor->wasRecentlyCreated) {
                $this->notifyHost($visitor);
            }

            return redirect()->route('visitor.pass', ['id' => $visitor->id])
                             ->with('success', 'Request sent to host. Awaiting approval.');

        } catch (Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function showPass($id)
    {
        $visitor = Visitor::findOrFail($id);
        return view('registration_success', compact('visitor'));
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS (SMS)
    |--------------------------------------------------------------------------
    */

    protected function notifyHost($visitor)
    {
        $host = User::where('name', $visitor->host_name)->first();
        if ($host && $host->phone) {
            $loginUrl = url('/login');
            $msg = "VMS: {$visitor->full_name} requests entry for '{$visitor->purpose}'. Approve/Reject via dashboard: {$loginUrl}";
            $this->sendSms($host->phone, $msg);
        }
    }

    protected function notifyHostCheckout($visitor)
    {
        $host = User::where('name', $visitor->host_name)->first();
        if ($host && $host->phone) {
            $time = Carbon::now()->format('H:i');
            $msg = "VMS Notify: Your visitor {$visitor->full_name} has checked out at {$time}.";
            $this->sendSms($host->phone, $msg);
        }
    }

    protected function notifyVisitor($visitor, $message)
    {
        if ($visitor->phone && $visitor->phone !== 'N/A') {
            $this->sendSms($visitor->phone, "Hi {$visitor->full_name}, " . $message);
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            return '+254' . substr($phone, 1);
        }
        return str_starts_with($phone, '254') ? '+' . $phone : '+254' . $phone;
    }

    private function sendSms($to, $message)
    {
        $username = env('AT_USERNAME');
        $apiKey   = env('AT_API_KEY');

        if (!$username || !$apiKey) return;

        try {
            $at = new AfricasTalking($username, $apiKey);
            $at->sms()->send([
                'to'      => $to, 
                'message' => $message,
                'from'    => env('AT_FROM', null)
            ]);
        } catch (Exception $e) {
            Log::error("AT Error: " . $e->getMessage());
        }
    }
}