<?php

namespace App\Http\Controllers;

use App\Models\CheckinLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckinRoleController extends Controller
{
    // Member: Show QR Code
    public function memberQR()
    {
        return view('checkin.member-qr');
    }

    // Staff: QR Scanner
    public function staffScanner()
    {
        return view('checkin.staff-scanner');
    }

    // Owner: Check-in History
    public function ownerHistory()
    {
        $members = User::where('role', 'User:Member')->get();
        return view('checkin.owner-history', compact('members'));
    }

    // Process QR Scan (for staff)
    public function processScan(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer',
            'type' => 'required|in:checkin,checkout'
        ]);

        $member = User::find($request->member_id);
        
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found']);
        }

        // Check if member has active membership
        $activeMembership = $member->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$activeMembership) {
            return response()->json(['success' => false, 'message' => 'Member has no active membership']);
        }

        // Check remaining visits
        if ($activeMembership->remaining_visits <= 0) {
            return response()->json(['success' => false, 'message' => 'No remaining visits']);
        }

        // Check for existing check-in without checkout
        $existingCheckin = CheckinLog::where('user_id', $member->id)
            ->where('type', 'checkin')
            ->whereNull('checkout_time')
            ->latest()
            ->first();

        if ($request->type === 'checkin' && $existingCheckin) {
            return response()->json(['success' => false, 'message' => 'Member already checked in']);
        }

        if ($request->type === 'checkout' && !$existingCheckin) {
            return response()->json(['success' => false, 'message' => 'No active check-in found']);
        }

        DB::beginTransaction();
        
        try {
            if ($request->type === 'checkin') {
                $checkin = CheckinLog::create([
                    'user_id' => $member->id,
                    'staff_id' => Auth::id(),
                    'type' => 'checkin',
                    'checkin_time' => now(),
                    'status' => 'active'
                ]);

                // Decrease remaining visits
                $activeMembership->decrement('remaining_visits');
            } else {
                $existingCheckin->update([
                    'checkout_time' => now(),
                    'status' => 'completed'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->type) . ' successful',
                'data' => [
                    'member_name' => $member->name,
                    'member_id' => $member->member_id,
                    'remaining_visits' => $activeMembership->remaining_visits
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Database error']);
        }
    }

    // Get history data (for owner)
    public function getHistoryData(Request $request)
    {
        $query = CheckinLog::with(['user', 'staff'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->member_id) {
            $query->where('user_id', $request->member_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Date range filter
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'custom':
                // Handle custom date range
                break;
        }

        $perPage = 10;
        $data = $query->paginate($perPage);

        // Calculate statistics
        $stats = [
            'total_checkins' => $query->where('type', 'checkin')->count(),
            'total_checkouts' => $query->where('type', 'checkout')->count(),
            'unique_members' => $query->distinct('user_id')->count('user_id'),
            'today_visits' => $query->whereDate('created_at', today())->count()
        ];

        return response()->json([
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total()
            ],
            'stats' => $stats
        ]);
    }

    // Get recent check-ins (for staff dashboard)
    public function getRecentCheckins()
    {
        $recent = CheckinLog::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'time' => $log->created_at->format('H:i'),
                    'member_name' => $log->user->name,
                    'type' => $log->type,
                    'status' => $log->status
                ];
            });

        return response()->json($recent);
    }

    // Manual check-in (for staff)
    public function manualCheckin(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string',
            'type' => 'required|in:checkin,checkout'
        ]);

        $member = User::where('member_id', $request->member_id)
                    ->orWhere('id', $request->member_id)
                    ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found']);
        }

        return $this->processScan($request);
    }

    // Export data (for owner)
    public function exportData(Request $request)
    {
        $query = CheckinLog::with(['user', 'staff'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as getHistoryData
        if ($request->member_id) {
            $query->where('user_id', $request->member_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($request->date_range === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->date_range === 'month') {
            $query->whereMonth('created_at', now()->month);
        }

        $data = $query->get();

        // Generate CSV
        $csv = "Date,Time,Member Name,Member ID,Type,Staff,Duration,Status\n";
        
        foreach ($data as $log) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log->created_at->format('Y-m-d'),
                $log->created_at->format('H:i:s'),
                $log->user->name,
                $log->user->member_id ?? $log->user->id,
                $log->type,
                $log->staff->name ?? 'System',
                $log->checkout_time ? $log->created_at->diff($log->checkout_time)->format('%H:%I:%S') : '-',
                $log->status
            );
        }

        $filename = 'checkin_history_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
