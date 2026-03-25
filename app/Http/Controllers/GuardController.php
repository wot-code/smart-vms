<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\User;
use Carbon\Carbon;

class GuardController extends Controller
{
    /**
     * 1. Show the Guard Dashboard
     * Displays visitors based on the 'checked_in_at' column name.
     */
    public function index(Request $request)
    {
        // Eager load 'host' and filter by today's date
        $query = Visitor::with('host')->whereDate('created_at', Carbon::today());

        // Handle Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('id_number', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        // Get all visitors for today
        $allVisitors = $query->latest()->get();

        /**
         * EXPECTED: Anyone 'pending' or 'approved' who HAS NOT checked in yet.
         * Using standardized column: checked_in_at
         */
        $expected = $allVisitors->filter(function ($v) {
            $status = strtolower($v->status);
            return in_array($status, ['pending', 'approved']) && is_null($v->checked_in_at);
        });

        /**
         * INSIDE: Anyone whose status is 'checked_in'.
         */
        $inside = $allVisitors->filter(function ($v) {
            return strtolower($v->status) === 'checked_in';
        });

        // Dashboard statistics
        $stats = [
            'expected_count'    => $expected->count(),
            'inside_count'      => $inside->count(),
            'checked_out_today' => $allVisitors->where('status', 'checked_out')->count(),
        ];

        return view('guard.dashboard', compact('stats', 'expected', 'inside'));
    }

    /**
     * 2. Show Manual Registration Form
     */
    public function create()
    {
        $hosts = User::all()->map(function($user) {
            return [
                'id'      => $user->id,
                'name'    => $user->name,
                'display' => $user->name . " (" . $user->email . ")"
            ];
        });

        return view('guard.register_visitor', compact('hosts'));
    }

    /**
     * 3. Store Manual Registration (Walk-ins)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'   => 'required|string|max:255',
            'host_name'   => 'required|string',
            'purpose'     => 'required|string',
            'id_number'   => 'nullable|string',
            'phone'       => 'nullable|string',
            'vehicle_reg' => 'nullable|string',
        ]);

        $visitor = Visitor::create([
            'full_name'     => $validated['full_name'],
            'host_name'     => $validated['host_name'],
            'purpose'       => $validated['purpose'],
            'id_number'     => $validated['id_number'],
            'phone'         => $validated['phone'],
            'vehicle_reg'   => $validated['vehicle_reg'],
            'type'          => 'Adult',
            'status'        => 'checked_in',
            'checked_in_at' => now(), // Correct column name
            'signature'     => 'MANUAL_ENTRY_GUARD',
        ]);
        
        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'MANUAL_REGISTRATION',
            'description' => "Guard manually registered and checked in: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('guard.dashboard')->with('success', 'Walk-in visitor registered and checked in.');
    }

    /**
     * 4. Process Check-in (The "Confirm Entry" button)
     */
    public function checkIn($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(), // Correct column name
        ]);
        
        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'CHECK_IN',
            'description' => "Guard checked in visitor: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', "{$visitor->full_name} has been checked in.");
    }

    /**
     * 5. Process Check-out (The "Check Out" button)
     */
    public function checkOut($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        $visitor->update([
            'status'         => 'checked_out',
            'checked_out_at' => now(), // Correct column name
        ]);
        
        \App\Models\AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'CHECK_OUT',
            'description' => "Guard checked out visitor: {$visitor->full_name}",
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', "{$visitor->full_name} has been checked out.");
    }
}