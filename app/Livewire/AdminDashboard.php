<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Livewire\Component;

class AdminDashboard extends Component
{
    /** Re-fetches all data on every poll cycle */
    public function render()
    {
        $today = Carbon::today();
        $insideStatuses  = ['Approved', 'approved', 'Inside', 'inside', 'Checked In', 'checked_in'];
        $pendingStatuses = ['Pending', 'pending'];

        // ── Stats ─────────────────────────────────────────────────────────
        $stats = [
            'total_visitors'   => Visitor::count(),
            'total_today'      => Visitor::whereDate('created_at', $today)->count(),
            'inside_now'       => Visitor::whereIn('status', $insideStatuses)
                                         ->whereNull('checked_out_at')
                                         ->count(),
            'pending_approval' => Visitor::whereIn('status', $pendingStatuses)->count(),
            'active_hosts'     => User::where('role', 'host')->count(),
            'approved_today'   => Visitor::whereIn('status', ['Approved', 'approved'])
                                         ->whereDate('updated_at', $today)
                                         ->count(),
        ];

        // ── Currently Inside ──────────────────────────────────────────────
        $insideNow = Visitor::whereIn('status', $insideStatuses)
                            ->whereNull('checked_out_at')
                            ->latest('checked_in_at')
                            ->take(8)
                            ->get();

        // ── Pending Approval ──────────────────────────────────────────────
        $pendingVisitors = Visitor::whereIn('status', $pendingStatuses)
                                  ->latest()
                                  ->take(8)
                                  ->get();

        // ── Recent Activity (last 20) ─────────────────────────────────────
        $recentActivity = Visitor::latest()->take(20)->get();

        // ── Recent Audit Logs ─────────────────────────────────────────────
        $recentAudit = AuditLog::with('user')->latest()->take(10)->get();

        return view('livewire.admin-dashboard', compact(
            'stats', 'insideNow', 'pendingVisitors', 'recentActivity', 'recentAudit'
        ));
    }
}
