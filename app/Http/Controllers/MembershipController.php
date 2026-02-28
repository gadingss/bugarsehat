<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPacket;
use App\Models\Transaction;
use App\Models\MembershipProduct;
use App\Models\CheckinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // If user is owner, they should manage members' memberships, not their personal status
        if ($user->hasRole('User:Owner') || $user->role === 'owner') {
            return redirect()->route('activation_order');
        }

        // Get active membership
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        // Get membership history
        $membershipHistory = Membership::where('user_id', $user->id)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get membership products if active membership exists
        $membershipProducts = collect();
        if ($activeMembership) {
            $membershipProducts = $activeMembership->membershipProducts()
                ->with('product')
                ->get();
        }

        // Get membership statistics
        $stats = [
            'total_memberships' => Membership::where('user_id', $user->id)->count(),
            'active_days' => $activeMembership ? $activeMembership->getRemainingDays() : 0,
            'total_visits' => CheckinLog::where('user_id', $user->id)->count(),
            'this_month' => CheckinLog::where('user_id', $user->id)
                ->whereMonth('checkin_time', now()->month)
                ->whereYear('checkin_time', now()->year)
                ->count(),
        ];

        // Get recent activities
        $recentActivities = CheckinLog::where('user_id', $user->id)
            ->orderBy('checkin_time', 'desc')
            ->take(5)
            ->get();

        // Get upcoming expiry warning
        $expiryWarning = null;
        if ($activeMembership) {
            $daysUntilExpiry = $activeMembership->getRemainingDays();
            if ($daysUntilExpiry <= 7 && $daysUntilExpiry > 0) {
                $expiryWarning = [
                    'days' => $daysUntilExpiry,
                    'message' => "Membership Anda akan berakhir dalam {$daysUntilExpiry} hari"
                ];
            }
        }

        $availablePackages = MembershipPacket::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return view('membership.index', compact(
            'user',
            'activeMembership',
            'availablePackages',
            'membershipHistory',
            'membershipProducts',
            'stats',
            'recentActivities',
            'expiryWarning'
        ));
    }

    public function history()
    {
        $user = Auth::user();
        $memberships = Membership::where('user_id', $user->id)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('membership.history', compact('user', 'memberships'));
    }

    public function renewal()
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        if (!$activeMembership) {
            return redirect()->route('membership.index')
                ->with('error', 'Anda tidak memiliki membership aktif untuk diperpanjang.');
        }

        $availablePackages = MembershipPacket::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return view('membership.renewal', compact('user', 'activeMembership', 'availablePackages'));
    }

    public function processRenewal(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'renewal_type' => 'required|in:extend,replace'
        ]);

        $user = Auth::user();
        $package = MembershipPacket::findOrFail($request->package_id);
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeMembership) {
            return redirect()->route('membership.index')
                ->with('error', 'Tidak ada membership aktif untuk diperpanjang.');
        }

        DB::beginTransaction();
        try {
            if ($request->renewal_type === 'extend') {
                // Extend current membership
                $newEndDate = Carbon::parse($activeMembership->end_date)
                    ->addDays($package->duration_days);

                $activeMembership->update([
                    'end_date' => $newEndDate,
                    'remaining_visits' => $activeMembership->remaining_visits + $package->visit_limit
                ]);

                $membership = $activeMembership;
            } else {
                // Replace current membership
                $activeMembership->update(['status' => 'replaced']);

                $startDate = Carbon::now();
                $endDate = $startDate->copy()->addDays($package->duration_days);

                $membership = Membership::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'type' => $package->price == 0 ? 'trial' : 'permanent',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'remaining_visits' => $package->visit_limit,
                    'status' => $package->price == 0 ? 'active' : 'inactive'
                ]);
            }

            // Create transaction if package has price
            if ($package->price > 0) {
                Transaction::create([
                    'user_id' => $user->id,
                    'product_id' => $package->id,
                    'transaction_date' => now(),
                    'amount' => $package->price,
                    'status' => 'pending'
                ]);

                DB::commit();
                return redirect()->route('membership.payment', $membership->id)
                    ->with('success', 'Perpanjangan membership berhasil! Silakan lakukan pembayaran.');
            } else {
                DB::commit();
                return redirect()->route('membership.success', $membership->id)
                    ->with('success', 'Membership berhasil diperpanjang!');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperpanjang membership: ' . $e->getMessage());
        }
    }

    public function payment($membershipId)
    {
        $membership = Membership::with(['package', 'user'])->findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($membership->status !== 'inactive' || $membership->package->price == 0) {
            return redirect()->route('membership.index')
                ->with('error', 'Halaman pembayaran tidak tersedia.');
        }

        return view('membership.payment', compact('membership'));
    }

    public function activate($membershipId)
    {
        $membership = Membership::with('package')->findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        DB::beginTransaction();
        try {
            $membership->update(['status' => 'active']);

            // Update transaction status if exists
            $transaction = Transaction::where('user_id', $membership->user_id)
                ->where('product_id', $membership->package_id)
                ->where('status', 'pending')
                ->first();

            if ($transaction) {
                $transaction->update(['status' => 'validated']);
            }

            DB::commit();
            return redirect()->route('membership.success', $membership->id)
                ->with('success', 'Membership berhasil diaktifkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan membership: ' . $e->getMessage());
        }
    }

    public function success($membershipId)
    {
        $membership = Membership::with(['package', 'user'])->findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('membership.success', compact('membership'));
    }

    public function cancel($membershipId)
    {
        $membership = Membership::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($membership->status !== 'inactive') {
            return redirect()->back()
                ->with('error', 'Hanya membership yang belum aktif yang dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $membership->update(['status' => 'cancelled']);

            // Cancel related transaction
            $transaction = Transaction::where('user_id', $membership->user_id)
                ->where('product_id', $membership->package_id)
                ->where('status', 'pending')
                ->first();

            if ($transaction) {
                $transaction->update(['status' => 'cancelled']);
            }

            DB::commit();
            return redirect()->route('membership.index')
                ->with('success', 'Membership berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal membatalkan membership: ' . $e->getMessage());
        }
    }

    public function upgrade()
    {
        $user = Auth::user();

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        if (!$activeMembership) {
            return redirect()->route('packet_membership')
                ->with('info', 'Anda belum memiliki membership aktif.');
        }

        // Get upgrade options (packages with higher price)
        $upgradePackages = MembershipPacket::where('is_active', true)
            ->where('price', '>', $activeMembership->package->price)
            ->orderBy('price', 'asc')
            ->get();

        return view('membership.upgrade', compact('activeMembership', 'upgradePackages'));
    }

    public function processUpgrade(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id'
        ]);

        $user = Auth::user();
        $newPackage = MembershipPacket::findOrFail($request->package_id);

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        if (!$activeMembership) {
            return back()->with('error', 'Membership aktif tidak ditemukan.');
        }

        // Calculate upgrade cost (prorated)
        $remainingDays = $activeMembership->getRemainingDays();
        $dailyRate = $newPackage->price / $newPackage->duration_days;
        $upgradeCost = $dailyRate * $remainingDays;

        DB::beginTransaction();
        try {
            // Update current membership to new package
            $activeMembership->update([
                'package_id' => $newPackage->id,
            ]);

            // Create transaction for upgrade cost
            Transaction::create([
                'user_id' => $user->id,
                'product_id' => $newPackage->id,
                'transaction_date' => Carbon::now(),
                'amount' => $upgradeCost,
                'status' => 'pending',
                'description' => 'Upgrade Membership ke ' . $newPackage->name
            ]);

            DB::commit();

            return redirect()->route('membership.payment', $activeMembership->id)
                ->with('success', 'Silakan lakukan pembayaran untuk upgrade membership.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memproses upgrade membership.');
        }
    }

    public function statistics()
    {
        $user = Auth::user();

        // Get comprehensive membership statistics
        $stats = [
            'total_spent' => Transaction::where('user_id', $user->id)
                ->where('status', 'validated')
                ->sum('amount'),
            'total_visits' => CheckinLog::where('user_id', $user->id)->count(),
            'average_duration' => $this->getAverageMembershipDuration($user->id),
            'favorite_time' => $this->getFavoriteVisitTime($user->id),
            'monthly_visits' => $this->getMonthlyVisitStats($user->id),
            'membership_timeline' => $this->getMembershipTimeline($user->id),
        ];

        return view('membership.statistics', compact('stats'));
    }

    public function benefits()
    {
        $user = Auth::user();

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('package')
            ->first();

        $membershipProducts = collect();
        $availableServices = collect();

        if ($activeMembership) {
            // Get products included in membership
            $membershipProducts = $activeMembership->membershipProducts()
                ->with('product')
                ->get();

            // Get available services for this membership level
            $availableServices = \App\Models\Service::where('is_active', true)
                ->where('required_membership_level', '<=', $this->getMembershipLevel($activeMembership->package))
                ->get();
        }

        return view('membership.benefits', compact('activeMembership', 'membershipProducts', 'availableServices'));
    }

    public function packages()
    {
        $user = Auth::user();

        // Get all active packages for member to choose from
        $packets = MembershipPacket::where('is_active', true)
            ->orderBy('price', 'asc')
            ->orderBy('duration_days', 'asc')
            ->get();

        // Check if user has active membership
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        return view('membership.packages', compact('packets', 'activeMembership'));
    }

    private function getAverageMembershipDuration($userId)
    {
        $memberships = Membership::where('user_id', $userId)
            ->where('status', '!=', 'pending')
            ->get();

        if ($memberships->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($memberships as $membership) {
            $startDate = Carbon::parse($membership->start_date);
            $endDate = $membership->status === 'active' ? Carbon::now() : Carbon::parse($membership->end_date);
            $totalDays += $startDate->diffInDays($endDate);
        }

        return round($totalDays / $memberships->count());
    }

    private function getFavoriteVisitTime($userId)
    {
        $checkins = CheckinLog::where('user_id', $userId)
            ->selectRaw('HOUR(checkin_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        if (!$checkins) {
            return null;
        }

        $hour = $checkins->hour;
        if ($hour < 12) {
            return 'Pagi (' . $hour . ':00)';
        } elseif ($hour < 17) {
            return 'Siang (' . $hour . ':00)';
        } else {
            return 'Sore/Malam (' . $hour . ':00)';
        }
    }

    private function getMonthlyVisitStats($userId)
    {
        $stats = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('M Y');

            $stats[$monthKey] = CheckinLog::where('user_id', $userId)
                ->whereMonth('checkin_time', $month->month)
                ->whereYear('checkin_time', $month->year)
                ->count();
        }

        return $stats;
    }

    private function getMembershipTimeline($userId)
    {
        return Membership::where('user_id', $userId)
            ->with('package')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($membership) {
                return [
                    'package_name' => $membership->package->name,
                    'start_date' => $membership->start_date,
                    'end_date' => $membership->end_date,
                    'status' => $membership->status,
                    'duration_days' => Carbon::parse($membership->start_date)->diffInDays(Carbon::parse($membership->end_date)),
                ];
            });
    }

    private function getMembershipLevel($package)
    {
        // Define membership levels based on price or package name
        $levels = [
            'Trial' => 1,
            'Silver' => 2,
            'Gold' => 3,
            'Platinum' => 4,
            'Diamond' => 5,
        ];

        return $levels[$package->name] ?? 1;
    }
}
