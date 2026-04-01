<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;
use AfricasTalking\SDK\AfricasTalking;

class VisitorController extends Controller
{
    /**
     * Set the system timezone consistently.
     */
    protected $tz = 'Africa/Nairobi';

    public function create()
    {
        $hosts = $this->getHostList();
        return view('visitor.register', compact('hosts'));
    }

    public function guardCreate()
    {
        $hosts = $this->getHostList();
        return view('guard.register_visitor', compact('hosts'));
    }

    /**
     * Dashboard Router: Robustly matches host name strings.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'guard') {
            return redirect()->route('guard.dashboard');
        }

        $is_admin = ($user->role === 'admin');

        // DATABASE FIX: TRIM and LOWER the column to prevent "0 Records" due to hidden spaces
        $query = $is_admin 
            ? Visitor::query() 
            : Visitor::whereRaw('LOWER(TRIM(host_name)) = ?', [strtolower(trim($user->name))]);

        // Stats Logic: Using whereRaw to ensure Case-Insensitivity for statuses
        $stats = [
            'total_visitors'   => (clone $query)->count(),
            'pending_approval' => (clone $query)->whereRaw('LOWER(status) = ?', ['pending'])->count(),
            'approved_today'   => (clone $query)->whereRaw('LOWER(status) = ?', ['approved'])
                                            ->whereDate('updated_at', Carbon::today($this->tz))
                                            ->count(),
            // Recognizes both 'Inside' and 'checked_in' from your DB
            'inside_now'       => (clone $query)->whereIn('status', ['Inside', 'checked_in', 'inside'])->count(),
            'active_hosts'     => $is_admin ? User::where('role', 'host')->count() : null,
        ];

        $visitors = $query->latest()->paginate(15);
        $viewTitle = $is_admin ? "Global Activity Monitor" : "My Visitor Requests";
        $viewPath = $is_admin ? 'admin.dashboard' : 'host.dashboard';

        return view($viewPath, compact('visitors', 'viewTitle', 'stats'));
    }

    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);
        $user = Auth::user();

        // Robust ownership check
        if ($user->role === 'host' && strtolower(trim($visitor->host_name)) !== strtolower(trim($user->name))) {
            abort(403, 'Unauthorized access to visitor details.');
        }

        return view('visitor.show', compact('visitor'));
    }

    /*
    |--------------------------------------------------------------------------
    | GUARD LOGIC
    |--------------------------------------------------------------------------
    */

    public function guardDashboard(Request $request)
    {
        $search = $request->input('search');
        $today = Carbon::today($this->tz);

        $stats = [
            'expected_count' => Visitor::whereRaw('LOWER(status) = ?', ['approved'])->whereNull('checked_in_at')->count(),
            'inside_count'   => Visitor::whereIn('status', ['Inside', 'checked_in', 'inside'])->count(),
            'checked_out_today' => Visitor::whereDate('checked_out_at', $today)->count(),
        ];

        $expected = Visitor::whereRaw('LOWER(status) = ?', ['approved'])
            ->whereNull('checked_in_at')
            ->when($search, function($q) use ($search) {
                return $q->where(function($sq) use ($search) {
                    $sq->where('full_name', 'LIKE', "%{$search}%")
                       ->orWhere('id_number', 'LIKE', "%{$search}%");
                });
            })->get();

        $inside = Visitor::whereIn('status', ['Inside', 'checked_in', 'inside'])
            ->when($search, function($q) use ($search) {
                return $q->where(function($sq) use ($search) {
                    $sq->where('full_name', 'LIKE', "%{$search}%")
                       ->orWhere('host_name', 'LIKE', "%{$search}%")
                       ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('guard.dashboard', compact('expected', 'inside', 'stats'));
    }

    public function guardStore(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'nullable|string', 
            'type'           => 'required|in:Adult,Minor',
            'host_name'      => 'required|string|max:255',
            'purpose'        => 'required|string',
            'id_number'      => 'nullable|string|max:50', 
            'vehicle_reg'    => 'nullable|string|max:20',
            'signature_data' => 'required|string',
        ]);

        try {
            $phone = $validated['phone'] ? $this->formatPhone($validated['phone']) : '+254000000000';
            
            $visitor = Visitor::create([
                'full_name'     => $validated['full_name'],
                'phone'         => $phone,
                'type'          => $validated['type'],
                'host_name'     => trim($validated['host_name']), 
                'purpose'       => $validated['purpose'],
                'id_number'     => $validated['id_number'] ?? 'WALK-IN',
                'vehicle_reg'   => $validated['vehicle_reg'],
                'signature'     => $validated['signature_data'],
                'status'        => 'Inside', 
                'checked_in_at' => Carbon::now($this->tz),
            ]);

            $this->notifyHost($visitor, true);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'ASSISTED_REGISTRATION',
                'description' => "Guard registered and checked in {$visitor->full_name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('guard.dashboard')->with('success', "{$visitor->full_name} has been checked in.");

        } catch (Exception $e) {
            Log::error("Guard Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors('Registration failed: ' . $e->getMessage());
        }
    }

    public function processCheckIn($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status' => 'Inside',
            'checked_in_at' => Carbon::now($this->tz), 
        ]);

        return back()->with('success', "{$visitor->full_name} is now inside.");
    }

    public function processCheckOut($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status' => 'Checked Out',
            'checked_out_at' => Carbon::now($this->tz), 
        ]);

        $this->notifyHostCheckout($visitor);

        return back()->with('success', "{$visitor->full_name} has checked out.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'nullable|string', 
            'type'           => 'required|in:Adult,Minor',
            'host_name'      => 'required|string|max:255',
            'purpose'        => 'required|string',
            'purpose_other'  => 'required_if:purpose,Other|nullable|string',
            'id_number'      => 'nullable|string|max:50', 
            'vehicle_reg'    => 'nullable|string|max:20',
            'signature_data' => 'required|string',
        ]);

        try {
            $phone = $validated['phone'] ? $this->formatPhone($validated['phone']) : '+254000000000';
            $finalPurpose = ($validated['purpose'] === 'Other') ? $validated['purpose_other'] : $validated['purpose'];

            // Clean Host Name before saving
            $cleanHost = trim($validated['host_name']);

            $visitor = Visitor::create([
                'full_name'     => $validated['full_name'],
                'phone'         => $phone,
                'type'          => $validated['type'],
                'host_name'     => $cleanHost,
                'purpose'       => $finalPurpose,
                'id_number'     => $validated['id_number'],
                'vehicle_reg'   => $validated['vehicle_reg'],
                'signature'     => $validated['signature_data'],
                'status'        => 'Pending'
            ]);

            $this->notifyHost($visitor);

            return redirect()->route('visitor.pass', ['id' => $visitor->id])
                             ->with('success', 'Awaiting host approval.');

        } catch (Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors('System error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CORE UTILITIES
    |--------------------------------------------------------------------------
    */

    protected function notifyHost($visitor, $isManual = false)
    {
        $host = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($visitor->host_name))])->first();
        
        if ($host && $host->phone && $host->phone !== '+254000000000') {
            $msg = $isManual 
                ? "VMS ALERT: {$visitor->full_name} is inside to see you."
                : "VMS: {$visitor->full_name} is at the gate. Approve: " . url('/login');
            
            $this->sendSms($host->phone, $msg);
        }
    }

    protected function notifyHostCheckout($visitor)
    {
        $host = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($visitor->host_name))])->first();
        if ($host && $host->phone && $host->phone !== '+254000000000') {
            $this->sendSms($host->phone, "VMS: Your visitor {$visitor->full_name} has checked out.");
        }
    }

    public function approve($id)
    {
        $visitor = Visitor::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && strtolower(trim($visitor->host_name)) !== strtolower(trim($user->name))) {
            abort(403);
        }

        $visitor->update(['status' => 'Approved']);
        $this->notifyVisitor($visitor, "Approved! Show your pass to the guard for entry.");

        return back()->with('success', "Visitor approved.");
    }

    public function reject($id)
    {
        $visitor = Visitor::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && strtolower(trim($visitor->host_name)) !== strtolower(trim($user->name))) {
            abort(403);
        }

        $visitor->update(['status' => 'Rejected']);
        $this->notifyVisitor($visitor, "Your visit request was declined by the host.");

        return back()->with('success', "Visitor rejected.");
    }

    private function getHostList()
    {
        return User::where('role', 'host')
            ->orderBy('name', 'asc')
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'display' => $user->name . " - " . ($user->department ?? 'Host')
            ]);
    }

    private function sendSms($to, $message)
    {
        $username = env('AT_USERNAME');
        $apiKey   = env('AT_API_KEY');
        
        if (!$username || !$apiKey || $to === '+254000000000') return;

        try {
            $at = new AfricasTalking($username, $apiKey);
            $at->sms()->send([
                'to'      => $this->formatPhone($to),
                'message' => $message
            ]);
        } catch (Exception $e) {
            Log::error("SMS Error: " . $e->getMessage());
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone)) return '+254000000000';
        if (str_starts_with($phone, '0')) return '+254' . substr($phone, 1);
        if (str_starts_with($phone, '254')) return '+' . $phone;
        return '+254' . $phone;
    }

    protected function notifyVisitor($visitor, $message)
    {
        if ($visitor->phone && $visitor->phone !== '+254000000000') {
            $this->sendSms($visitor->phone, "Hi {$visitor->full_name}, " . $message);
        }
    }

    public function showPass($id) { 
        $visitor = Visitor::findOrFail($id); 
        return view('registration_success', compact('visitor')); 
    }

    /**
     * Offline Sync Endpoint — Called by the Service Worker when connectivity is restored.
     * Accepts a JSON payload from IndexedDB and saves the visitor record.
     */
    public function syncOffline(Request $request)
    {
        $data = $request->json()->all();

        $validated = validator($data, [
            'full_name'      => 'required|string|max:255',
            'phone'          => 'nullable|string',
            'type'           => 'required|in:Adult,Minor,Contractor',
            'host_name'      => 'required|string|max:255',
            'purpose'        => 'required|string',
            'id_number'      => 'nullable|string|max:50',
            'vehicle_reg'    => 'nullable|string|max:20',
            'signature_data' => 'required|string',
        ])->validate();

        try {
            $phone = isset($validated['phone']) && $validated['phone']
                ? $this->formatPhone($validated['phone'])
                : '+254000000000';

            $visitor = Visitor::create([
                'full_name'   => $validated['full_name'],
                'phone'       => $phone,
                'type'        => $validated['type'],
                'host_name'   => trim($validated['host_name']),
                'purpose'     => $validated['purpose'],
                'id_number'   => $validated['id_number'] ?? 'OFFLINE',
                'vehicle_reg' => $validated['vehicle_reg'] ?? null,
                'signature'   => $validated['signature_data'],
                'status'      => 'Pending',
            ]);

            AuditLog::create([
                'user_id'     => null,
                'action'      => 'OFFLINE_SYNC',
                'description' => "Visitor {$visitor->full_name} registered offline and synced on reconnection.",
                'ip_address'  => $request->ip(),
            ]);

            $this->notifyHost($visitor);

            return response()->json(['success' => true, 'visitor_id' => $visitor->id], 201);

        } catch (Exception $e) {
            Log::error('Offline sync failed: ' . $e->getMessage());
            return response()->json(['error' => 'Sync failed'], 500);
        }
    }
}