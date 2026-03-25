<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visitor;
use App\Models\AuditLog; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Traffic Controller
     */
    public function index()
    {
        $user = Auth::user();
        
        // Standardize the role string to prevent Capitalization bugs
        $role = strtolower(trim($user->role));

        // 1. Role-Based Redirection
        if ($role === 'guard') {
            return redirect()->route('guard.dashboard');
        }

        if ($role === 'host') {
            $viewTitle = "My Visitors Portal";
            
            // FIX APPLIED HERE:
            // Changed LOWER(host) to LOWER(host_name) to fix the 500 Error!
            $currentUserName = strtolower($user->name);
            
            $visitors = Visitor::where(function($query) use ($currentUserName, $user) {
                                    $query->whereRaw('LOWER(host_name) = ?', [$currentUserName])
                                          ->orWhere('host_id', $user->id);
                                })
                                ->latest()
                                ->paginate(10);
                                
            return view('host.dashboard', compact('visitors', 'viewTitle'));
        }

        // 2. Admin Logic: Handled via Livewire polling now
        $viewTitle = "System Activity Log";
        return view('admin.dashboard', compact('viewTitle'));
    }

    /**
     * Objective 4: Regulatory Compliance - Generate Printable Report
     */
    public function printReport(Request $request)
    {
        $source = $request->get('source', 'visitor');
        $viewTitle = "System Report";

        if ($source === 'audit') {
            $query = AuditLog::with('user')->latest();
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }
            $logs = $query->get();
            return view('admin.reports.print', compact('logs', 'viewTitle'));
        }

        // Default: Visitor Report
        $query = Visitor::orderBy('created_at', 'desc');
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        $visitors = $query->get();
        return view('admin.reports.print_visitors', compact('visitors', 'viewTitle'));
    }

    /**
     * Objective 3: Audit Trail - Show System Actions
     */
    public function showAuditLogs(Request $request)
    {
        $viewTitle = "Security Audit Trail";
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        return view('admin.audit_logs', compact('logs', 'viewTitle'));
    }

    /**
     * Detailed Analytics & Traffic Charts
     */
    public function analytics()
    {
        $viewTitle = "System Analytics";
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

        // FIX: Changed visitor_type to type to match the database column!
        $visitorTypes = Visitor::select('type as visitor_type', DB::raw('count(*) as total'))
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

        return view('admin.analytics', compact('topHosts', 'visitorTypes', 'dailyTraffic', 'stats', 'viewTitle'));
    }

    public function listUsers()
    {
        $viewTitle = "User Management";
        $users = User::latest()->paginate(10);
        return view('admin.users_index', compact('users', 'viewTitle'));
    }

    public function createHost()
    {
        $viewTitle = "Register New User";
        return view('admin.create_host', compact('viewTitle'));
    }

    public function storeHost(Request $request)
    {
        if ($request->has('phone')) {
            $request->merge(['phone' => $this->formatPhone($request->phone)]);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|unique:users,phone',
            'role'     => 'required|in:host,guard,admin,Host,Guard,Admin',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'],
                'role'     => strtolower($validated['role']),
                'password' => Hash::make($validated['password']),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'USER_CREATION',
                'description' => "Admin created new {$user->role} account: {$user->name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.users_index')
                ->with('success', "Account for {$user->name} has been provisioned.");
        
        } catch (\Exception $e) {
            Log::error("Failed to create user: " . $e->getMessage());
            return back()->withInput()->with('error', 'Unable to create user. Please check the system logs.');
        }
    }

    public function showUser($id)
    {
        $viewTitle = "User Profile";
        $user = User::findOrFail($id);
        
        $visitors = collect();
        if (strtolower($user->role) === 'host') {
            $visitors = Visitor::whereRaw('LOWER(host_name) = ?', [strtolower($user->name)])
                               ->orWhere('host_id', $user->id)
                               ->latest()
                               ->take(50)
                               ->get();
        }
        
        return view('admin.show_user', compact('user', 'visitors', 'viewTitle'));
    }

    public function editUser($id)
    {
        $viewTitle = "Edit User Profile";
        $user = User::findOrFail($id);
        return view('admin.edit_host', compact('user', 'viewTitle'));
    }

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
            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone']
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'USER_UPDATE',
                'description' => "Admin updated profile for user: {$user->name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.users_index')
                ->with('success', "Updates saved for {$user->name}.");

        } catch (\Exception $e) {
            Log::error("Failed to update user: " . $e->getMessage());
            return back()->with('error', 'Error: Could not update user details.');
        }
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Operation Denied: You cannot delete yourself.');
        }

        try {
            $userName = $user->name;

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'USER_DELETION',
                'description' => "Admin permanently deleted user account: $userName",
                'ip_address' => request()->ip(),
            ]);

            $user->delete();
            return redirect()->route('admin.users_index')
                ->with('success', "User $userName has been successfully deleted.");
        } catch (\Exception $e) {
            Log::error("Failed to delete user: " . $e->getMessage());
            return back()->with('error', 'Delete Failed: User may be linked to system records.');
        }
    }

    public function clearAuditLogs()
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SYSTEM_PURGE',
            'description' => "User manually purged the System Audit Trail records.",
            'ip_address' => request()->ip(),
        ]);

        AuditLog::where('action', '!=', 'SYSTEM_PURGE')->delete();
        return back()->with('success', 'The system audit trail has been cleared.');
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) return '+254' . substr($phone, 1);
        if (str_starts_with($phone, '254') && strlen($phone) == 12) return '+' . $phone;
        return (str_starts_with($phone, '+')) ? $phone : '+' . $phone;
    }

    // ==========================================
    // HOST ACTIONS (Accept / Reject / Check Out)
    // ==========================================

    /**
     * Host Action: Accept Visitor
     */
    public function acceptVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status' => 'Approved', 
        ]);

        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'VISITOR_APPROVED',
            'description' => "Host approved visit request for: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);

        // FIX: Changed from visitor_name to full_name so the flash message works
        return back()->with('success', "You have accepted {$visitor->full_name}.");
    }

    /**
     * Host Action: Reject Visitor
     */
    public function rejectVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status' => 'Rejected', 
        ]);

        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'VISITOR_REJECTED',
            'description' => "Host rejected visit request for: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);

        // FIX: Changed from visitor_name to full_name
        return back()->with('error', "You have rejected {$visitor->full_name}.");
    }

    /**
     * Host Action: Check Out Visitor
     */
    public function checkoutVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status' => 'Checked Out',
            'checked_out_at' => Carbon::now(), 
        ]);

        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'HOST_CHECKOUT',
            'description' => "Host confirmed check-out for visitor: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);

        // FIX: Changed from visitor_name to full_name
        return back()->with('success', "{$visitor->full_name} has been checked out.");
    }
}