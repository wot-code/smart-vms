<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Main Admin Dashboard (System Activity Log)
     * Displays summary cards and the list of all visitors.
     */
    public function index()
    {
        $visitors = Visitor::latest()->get();
        
        // Summary data for the top dashboard cards
        // 'inside' counts visitors who are Approved but haven't checked out yet
        $stats = [
            'total_today' => Visitor::whereDate('created_at', Carbon::today())->count(),
            'pending'     => Visitor::where('status', 'Pending')->count(),
            'inside'      => Visitor::where('status', 'Approved')
                                    ->whereNull('checked_out_at')
                                    ->count(),
        ];

        $viewTitle = "System Activity Log";

        return view('dashboard', compact('visitors', 'stats', 'viewTitle'));
    }

    /**
     * Display Detailed Analytics: Charts, Traffic Trends, and Categories.
     */
    public function analytics()
    {
        // 1. Summary Statistics for Analytics Cards
        $stats = [
            'total_visitors' => Visitor::count(),
            'today_count'    => Visitor::whereDate('created_at', Carbon::today())->count(),
            'pending_now'    => Visitor::where('status', 'Pending')->count(),
            'approved_today' => Visitor::where('status', 'Approved')
                                        ->whereDate('updated_at', Carbon::today())->count(),
        ];

        // 2. Top 5 Busiest Hosts (Ranked by visitor count)
        $topHosts = Visitor::select('host_name', DB::raw('count(*) as total'))
            ->whereNotNull('host_name')
            ->groupBy('host_name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 3. Distribution of Visitor Types
        $visitorTypes = Visitor::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        // 4. Weekly Traffic Trend (Filling gaps with zero for smooth charts)
        $rawTraffic = Visitor::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $dailyTraffic = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyTraffic->push([
                'date' => now()->subDays($i)->format('D, M d'),
                'total' => $rawTraffic->get($date, 0)
            ]);
        }

        return view('admin.analytics', compact('topHosts', 'visitorTypes', 'dailyTraffic', 'stats'));
    }

    /**
     * Show the form to create a new Host.
     */
    public function createHost()
    {
        return view('admin.create_host');
    }

    /**
     * Save the new Host to the database.
     */
    public function storeHost(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        // Standardize phone format for Kenya (Africa's Talking compatible)
        $phone = $validated['phone'];
        if (str_starts_with($phone, '0')) {
            $phone = '+254' . substr($phone, 1);
        }

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $phone,
            'role'     => 'host',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'New Host registered successfully!');
    }

    /**
     * List all hosts for the Admin to see.
     */
    public function listUsers()
    {
        $users = User::where('role', 'host')->latest()->get();
        return view('admin.users_index', compact('users'));
    }

    /**
     * Show the form to edit an existing Host.
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_host', compact('user'));
    }

    /**
     * Update Host details in the database.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:6|confirmed', 
        ]);

        $phone = $validated['phone'];
        if (str_starts_with($phone, '0')) {
            $phone = '+254' . substr($phone, 1);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Host updated successfully!');
    }

    /**
     * Remove a Host from the system.
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Security: Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Host account removed successfully.');
    }
}