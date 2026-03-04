<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;
// Africa's Talking SDK
use AfricasTalking\SDK\AfricasTalking;

class VisitorController extends Controller
{
    /**
     * Display the dashboard with privacy-based filtering.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $visitors = Visitor::orderBy('created_at', 'desc')->get();
            $viewTitle = "All System Activities (Admin)";
            
            $stats = [
                'total_today' => Visitor::whereDate('created_at', Carbon::today())->count(),
                'pending'     => Visitor::where('status', 'Pending')->count(),
                'inside'      => Visitor::where('status', 'Approved')->count(),
            ];
        } else {
            $visitors = Visitor::where('host_name', $user->name)
                               ->orderBy('created_at', 'desc')
                               ->get();
            $viewTitle = "My Personal Visitors";
            $stats = null;
        }

        return view('dashboard', compact('visitors', 'viewTitle', 'stats'));
    }

    /**
     * View specific visitor pass
     */
    public function showPass($id)
    {
        $visitor = Visitor::findOrFail($id);
        return view('registration_success', compact('visitor'));
    } 
    
    /**
     * View detailed visitor profile with security check
     */
    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $visitor->host_name !== Auth::user()->name) {
            abort(403, 'Unauthorized access to visitor details.');
        }

        return view('admin.visitor_details', compact('visitor'));
    }

    /**
     * Show the registration form.
     */
    public function create()
    {
        $hosts = User::where('role', 'host')
                     ->orderBy('name', 'asc')
                     ->get(); 
        
        return view('register_visitor', compact('hosts'));
    }

    /**
     * Store the visitor registration data.
     * FIXED: Prevents duplication using updateOrCreate logic.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:15',
            'type'           => 'required|in:Adult,Minor,Delivery,Contractor',
            'host_name'      => 'required|string|max:255',
            'purpose'        => 'required|string',
            'guardian_name'  => 'required_if:type,Minor|nullable|string|max:255',
            'id_number'      => 'required|string|max:50',
            'vehicle_reg'    => 'nullable|string|max:20',
            'signature_data' => 'required|string',
        ]);

        try {
            // Normalize phone number
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '+254' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '+')) {
                $phone = '+254' . $phone; 
            }

            /**
             * FIX: Duplication Prevention
             * If an ID Number already has a 'Pending' request, update it.
             * Otherwise, create a new one.
             */
            $visitor = Visitor::updateOrCreate(
                [
                    'id_number' => $validated['id_number'],
                    'status'    => 'Pending'
                ],
                [
                    'full_name'     => $validated['full_name'],
                    'phone'         => $phone,
                    'type'          => $validated['type'],
                    'guardian_name' => $validated['guardian_name'] ?? null,
                    'host_name'     => $validated['host_name'],
                    'purpose'       => $validated['purpose'],
                    'vehicle_reg'   => $validated['vehicle_reg'],
                    'signature'     => $validated['signature_data'], 
                    'check_in'      => Carbon::now(), 
                ]
            );

            // Notify host only if this is a fresh registration (prevents spam on refresh)
            if ($visitor->wasRecentlyCreated) {
                $this->notifyHost($visitor);
            }

            // IMPORTANT: Redirect to showPass instead of returning view directly
            // This prevents form re-submission if the user hits "Refresh"
            return redirect()->route('visitor.pass', ['id' => $visitor->id]);

        } catch (Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Approve visitor
     */
    public function approve($id)
    {
        try {
            $visitor = Visitor::findOrFail($id);
            
            if (Auth::user()->role !== 'admin' && $visitor->host_name !== Auth::user()->name) {
                return back()->withErrors('Unauthorized action.');
            }

            $visitor->update(['status' => 'Approved']);
            $this->notifyVisitor($visitor, "Approved! You are cleared to enter. Please proceed.");

            return back()->with('success', "Visitor {$visitor->full_name} has been approved.");
        } catch (Exception $e) {
            return back()->withErrors('Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject visitor
     */
    public function reject($id)
    {
        try {
            $visitor = Visitor::findOrFail($id);
            
            if (Auth::user()->role !== 'admin' && $visitor->host_name !== Auth::user()->name) {
                return back()->withErrors('Unauthorized action.');
            }

            $visitor->update(['status' => 'Rejected']);
            $this->notifyVisitor($visitor, "Entry Denied. Your request to see {$visitor->host_name} was declined.");

            return back()->with('success', "Visitor {$visitor->full_name} has been rejected.");
        } catch (Exception $e) {
            return back()->withErrors('Rejection failed: ' . $e->getMessage());
        }
    }

    /**
     * Log the exit time
     */
    public function checkout($id)
    {
        try {
            $visitor = Visitor::findOrFail($id);
            $visitor->update([
                'checked_out_at' => Carbon::now(), 
                'status' => 'Checked Out'
            ]);

            return back()->with('success', "{$visitor->full_name} has been checked out.");
        } catch (Exception $e) {
            return back()->withErrors('Checkout failed: ' . $e->getMessage());
        }
    }

    protected function notifyHost($visitor)
    {
        $host = User::where('name', $visitor->host_name)->first();
        if ($host && $host->phone) {
            $message = "VMS Alert: {$visitor->full_name} is here to see you for '{$visitor->purpose}'. Please login to Approve/Reject.";
            $this->sendSms($host->phone, $message);
        }
    }

    protected function notifyVisitor($visitor, $message)
    {
        if ($visitor->phone) {
            $fullMessage = "Hi {$visitor->full_name}, " . $message;
            $this->sendSms($visitor->phone, $fullMessage);
        }
    }

    private function sendSms($to, $message)
    {
        $username = env('AT_USERNAME');
        $apiKey   = env('AT_API_KEY');

        if (!$username || !$apiKey) {
            Log::warning("Africa's Talking credentials not set in .env");
            return;
        }

        try {
            $at = new AfricasTalking($username, $apiKey);
            $sms = $at->sms();

            $sms->send([
                'to'      => $to, 
                'message' => $message,
                'from'    => env('AT_FROM', null)
            ]);

            Log::info("SMS sent to {$to}");
        } catch (Exception $e) {
            Log::error("Africa's Talking Error: " . $e->getMessage());
        }
    }
}