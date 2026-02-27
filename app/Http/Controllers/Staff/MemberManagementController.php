<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use App\Models\MembershipPacket;
use App\Models\Transaction;
use App\Models\CheckinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MemberManagementController extends Controller
{
    public function index()
    {
        // Get statistics for staff dashboard
        $stats = [
            'total_members' => User::where('role', 'member')->count(),
            'active_members' => Membership::where('status', 'active')
                ->where('end_date', '>=', now())
                ->count(),
            'new_members_today' => User::where('role', 'member')
                ->whereDate('created_at', today())
                ->count(),
            'expiring_soon' => Membership::where('status', 'active')
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count(),
        ];

        // Get recent member activities
        $recentActivities = CheckinLog::with(['user', 'membership.package'])
            ->orderBy('checkin_time', 'desc')
            ->take(10)
            ->get();

        // Get members with expiring memberships
        $expiringMemberships = Membership::with(['user', 'package'])
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->orderBy('end_date', 'asc')
            ->take(5)
            ->get();

        return view('staff.member-management.index', compact('stats', 'recentActivities', 'expiringMemberships'));
    }

    public function members(Request $request)
    {
        if ($request->ajax()) {
            $members = User::with(['memberships.package'])
                ->where('role', 'member')
                ->select('users.*');

            return DataTables::of($members)
                ->addColumn('membership_status', function ($member) {
                    $activeMembership = $member->memberships()
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->with('package')
                        ->first();

                    if ($activeMembership) {
                        $remainingDays = Carbon::parse($activeMembership->end_date)->diffInDays(now());
                        $badgeClass = $remainingDays <= 7 ? 'badge-warning' : 'badge-success';
                        return '<span class="badge ' . $badgeClass . '">' . $activeMembership->package->name . '</span>';
                    }

                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->addColumn('last_visit', function ($member) {
                    $lastCheckin = CheckinLog::where('user_id', $member->id)
                        ->orderBy('checkin_time', 'desc')
                        ->first();

                    return $lastCheckin ? Carbon::parse($lastCheckin->checkin_time)->diffForHumans() : 'Belum pernah';
                })
                ->addColumn('total_visits', function ($member) {
                    return CheckinLog::where('user_id', $member->id)->count();
                })
                ->addColumn('actions', function ($member) {
                    return '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-light-primary" onclick="viewMember(' . $member->id . ')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light-warning" onclick="editMember(' . $member->id . ')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light-success" onclick="manageMembership(' . $member->id . ')">
                                <i class="fas fa-id-card"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['membership_status', 'actions'])
                ->make(true);
        }

        return view('staff.member-management.members');
    }

    public function show($id)
    {
        $member = User::with(['memberships.package'])->findOrFail($id);
        
        // Get member statistics
        $stats = [
            'total_visits' => CheckinLog::where('user_id', $id)->count(),
            'this_month_visits' => CheckinLog::where('user_id', $id)
                ->whereMonth('checkin_time', now()->month)
                ->count(),
            'total_spent' => Transaction::where('user_id', $id)
                ->where('status', 'validated')
                ->sum('amount'),
            'membership_count' => Membership::where('user_id', $id)->count(),
        ];

        // Get active membership
        $activeMembership = Membership::where('user_id', $id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        // Get membership history
        $membershipHistory = Membership::where('user_id', $id)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent check-ins
        $recentCheckins = CheckinLog::where('user_id', $id)
            ->orderBy('checkin_time', 'desc')
            ->take(10)
            ->get();

        // Get transaction history
        $transactions = Transaction::where('user_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'member' => $member,
            'stats' => $stats,
            'activeMembership' => $activeMembership,
            'membershipHistory' => $membershipHistory,
            'recentCheckins' => $recentCheckins,
            'transactions' => $transactions,
        ]);
    }

    public function edit($id)
    {
        $member = User::findOrFail($id);
        return response()->json($member);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        $member = User::findOrFail($id);
        $member->update($request->only([
            'name', 'email', 'phone', 'address', 'date_of_birth', 
            'gender', 'emergency_contact', 'emergency_phone'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data member berhasil diperbarui'
        ]);
    }

    public function manageMembership($id)
    {
        $member = User::findOrFail($id);
        
        $activeMembership = Membership::where('user_id', $id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        $availablePackages = MembershipPacket::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'member' => $member,
            'activeMembership' => $activeMembership,
            'availablePackages' => $availablePackages,
        ]);
    }

    public function assignMembership(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'start_date' => 'required|date',
            'payment_status' => 'required|in:paid,pending',
        ]);

        $member = User::findOrFail($id);
        $package = MembershipPacket::findOrFail($request->package_id);

        DB::beginTransaction();
        try {
            // Deactivate existing active memberships
            Membership::where('user_id', $id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Create new membership
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addDays($package->duration_days);

            $membership = Membership::create([
                'user_id' => $id,
                'package_id' => $package->id,
                'type' => $package->price == 0 ? 'trial' : 'permanent',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remaining_visits' => $package->max_visits ?? 999,
                'status' => 'active'
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $id,
                'product_id' => $package->id,
                'transaction_date' => now(),
                'amount' => $package->price,
                'status' => $request->payment_status == 'paid' ? 'validated' : 'pending',
                'description' => 'Membership ' . $package->name . ' - Assigned by Staff'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Membership berhasil diberikan kepada ' . $member->name
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function extendMembership(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'reason' => 'required|string|max:255',
        ]);

        $activeMembership = Membership::where('user_id', $id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->firstOrFail();

        $newEndDate = Carbon::parse($activeMembership->end_date)->addDays($request->days);
        
        $activeMembership->update([
            'end_date' => $newEndDate
        ]);

        // Log the extension
        DB::table('membership_extensions')->insert([
            'membership_id' => $activeMembership->id,
            'extended_by' => auth()->id(),
            'days_added' => $request->days,
            'reason' => $request->reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership berhasil diperpanjang ' . $request->days . ' hari'
        ]);
    }

    public function suspendMembership(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $activeMembership = Membership::where('user_id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $activeMembership->update([
            'status' => 'suspended'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership berhasil disuspend'
        ]);
    }

    public function reactivateMembership($id)
    {
        $suspendedMembership = Membership::where('user_id', $id)
            ->where('status', 'suspended')
            ->firstOrFail();

        $suspendedMembership->update([
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership berhasil diaktifkan kembali'
        ]);
    }

    public function membershipReport()
    {
        $report = [
            'total_active' => Membership::where('status', 'active')
                ->where('end_date', '>=', now())
                ->count(),
            'expiring_this_week' => Membership::where('status', 'active')
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count(),
            'expired_this_month' => Membership::where('status', 'expired')
                ->whereMonth('end_date', now()->month)
                ->count(),
            'new_this_month' => Membership::whereMonth('start_date', now()->month)
                ->count(),
            'by_package' => Membership::join('membership_packages', 'memberships.package_id', '=', 'membership_packages.id')
                ->where('memberships.status', 'active')
                ->where('memberships.end_date', '>=', now())
                ->groupBy('membership_packages.name')
                ->selectRaw('membership_packages.name, COUNT(*) as count')
                ->get(),
        ];

        return view('staff.member-management.report', compact('report'));
    }
}
