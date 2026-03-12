<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visitor;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class AdminController extends Controller
{
    /**
     * Dashboard Traffic Controller
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Role-Based Redirection
        if ($user->role === 'guard') {
            return redirect()->route('guard.dashboard');
        }

        if ($user->role === 'host') {
            $viewTitle = "My Visitors Portal";
            $visitors = Visitor::where('host_name', $user->name)
                                ->latest()
                                ->paginate(10);
                                
            return view('host.dashboard', compact('visitors', 'viewTitle'));
        }

        // 2. Admin Logic: Global Analytics & Activity
        $viewTitle = "System Activity Log";
        $visitors = Visitor::latest()->paginate(15);
        
        $stats = [
            'total_today'    => Visitor::whereDate('created_at', Carbon::today())->count(),
            'total_visitors' => Visitor::count(),
            'pending'        => Visitor::whereIn('status', ['Pending', 'pending'])->count(),
            'inside'         => Visitor::whereIn('status', ['Approved', 'approved', 'Inside', 'Checked In', 'checked-in'])
                                        ->whereNull('checked_out_at')
                                        ->count(),
        ];

        return view('admin.dashboard', compact('visitors', 'stats', 'viewTitle'));
    }

    /**
     * Detailed Analytics & Traffic Charts
     */
    public function analytics()
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'today_count'    => Visitor::whereDate('created_at', Carbon::today())->count(),
            'pending_now'    => Visitor::whereIn('status', ['Pending', 'pending'])->count(),
            'approved_today' => Visitor::whereIn('status', ['Approved', 'approved'])
                                        ->whereDate('updated_at', Carbon::today())->count(),
        ];

        $topHosts = Visitor::select('host_name', DB::raw('count(*) as total'))
            ->whereNotNull('host_name')
            ->groupBy('host_name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $visitorTypes = Visitor::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        $rawTraffic = Visitor::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $dailyTraffic = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateKey = $dateObj->format('Y-m-d');
            
            $dailyTraffic->push([
                'label' => $dateObj->format('D, M d'),
                'total' => $rawTraffic->get($dateKey, 0)
            ]);
        }

        return view('admin.analytics', compact('topHosts', 'visitorTypes', 'dailyTraffic', 'stats'));
    }

    public function visitorDetails($id)
    {
        $visitor = Visitor::findOrFail($id);
        return view('admin.visitor_details', compact('visitor'));
    }

    public function listUsers()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users_index', compact('users'));
    }

    public function createHost()
    {
        return view('admin.create_host');
    }

    /**
     * Refined storeHost with Detailed Modal Feedback
     */
    public function storeHost(Request $request)
    {
        // Pre-format phone
        if ($request->has('phone')) {
            $request->merge(['phone' => $this->formatPhone($request->phone)]);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|unique:users,phone',
            'role'     => 'required|in:host,guard,admin',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'],
                'role'     => strtolower($validated['role']),
                'password' => Hash::make($validated['password']),
            ]);

            // Detailed message for the SweetAlert2 Modal
            $roleLabel = ucfirst($validated['role']);
            return redirect()->route('admin.users_index')
                ->with('success', "Success! New $roleLabel account for {$validated['name']} has been provisioned.");
        
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Critical System Failure: Unable to create user. Please check database logs.');
        }
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_host', compact('user'));
    }

    /**
     * Refined updateUser with Detailed Modal Feedback
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->has('phone')) {
            $request->merge(['phone' => $this->formatPhone($request->phone)]);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:8|confirmed', 
        ]);

        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];

            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            return redirect()->route('admin.users_index')
                ->with('success', "Updates saved! The profile for {$user->name} has been refreshed.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: Could not update user details.');
        }
    }

    /**
     * Refined destroyUser with Detailed Modal Feedback
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Operation Denied: You cannot delete the account you are currently logged into.');
        }

        try {
            $userName = $user->name;
            $user->delete();
            return redirect()->route('admin.users_index')
                ->with('success', "User Removed: $userName has been successfully deleted from the system.");
        } catch (\Exception $e) {
            return back()->with('error', 'Delete Failed: This user might be linked to active visitor records.');
        }
    }

    /**
     * Security Audit Logs with Search
     */
    public function securityLogs(Request $request)
    {
        $query = SecurityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('ip_address', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhere('url', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        return view('admin.security_logs', compact('logs'));
    }

    public function clearSecurityLogs()
    {
        SecurityLog::truncate();

        SecurityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PURGED_SECURITY_LOGS',
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'user_agent' => request()->userAgent()
        ]);
        
        return back()->with('success', 'The security audit trail has been cleared and reset.');
    }

    public function exportSecurityLogs()
    {
        $logs = SecurityLog::with('user')->latest()->limit(1000)->get();
        
        $data = [
            'title' => 'Security Audit Report',
            'date'  => now()->format('d M, Y h:i A'),
            'logs'  => $logs,
            'admin' => Auth::user()->name
        ];

        $pdf = Pdf::loadView('admin.pdf_security_logs', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Audit-Report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Helper to standardize phone numbers (Kenya format)
     */
    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '+254' . substr($phone, 1);
        }
        
        if (str_starts_with($phone, '254') && strlen($phone) == 12) {
            return '+' . $phone;
        }

        return (str_starts_with($phone, '+')) ? $phone : '+' . $phone;
    }
}