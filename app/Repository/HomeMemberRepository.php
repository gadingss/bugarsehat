<?php
namespace App\Repository;

use App\Models\Membership;
use App\Models\CheckinLog;
use App\Models\Product;
use App\Models\ServiceTransaction;
use App\Models\Transaction;
use App\Models\MembershipPacket;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeMemberRepository
{

    public static function generate($request = [])
    {
        try {
            $user = Auth::user();
            $today = now()->startOfDay();

            // 1. Logika Membership Aktif yang Lebih Akurat
            // Mencari membership yang sudah dimulai dan belum berakhir.
            $activeMembership = Membership::where('user_id', $user->id)
                ->whereIn('status', ['active', 'paid'])
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->with('package')
                ->orderBy('created_at', 'desc') // Ambil yang paling baru jika ada tumpang tindih
                ->first();

            // 2. Query yang Disederhanakan (asumsi migrasi sudah jalan)
            $activePromos = Product::active()->promo()->take(5)->get();

            // 3. Optimasi Query Statistik Check-in (dari 4 query menjadi 1)
            $checkinStatsData = CheckinLog::where('user_id', $user->id)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN DATE(checkin_time) = CURDATE() THEN 1 ELSE 0 END) as today"),
                    DB::raw("SUM(CASE WHEN YEARWEEK(checkin_time, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) as this_week"),
                    DB::raw("SUM(CASE WHEN MONTH(checkin_time) = MONTH(CURDATE()) AND YEAR(checkin_time) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as this_month")
                )
                ->first();

            $checkinStats = [
                'total' => $checkinStatsData->total ?? 0,
                'today' => $checkinStatsData->today ?? 0,
                'this_week' => $checkinStatsData->this_week ?? 0,
                'this_month' => $checkinStatsData->this_month ?? 0,
            ];

            // 4. Query Umum yang Disederhanakan
            $recentTransactions = Transaction::where('user_id', $user->id)
                ->with('product')
                ->latest('transaction_date')
                ->take(5)
                ->get();

            $upcomingServices = ServiceTransaction::where('user_id', $user->id)
                ->where('status', 'scheduled')
                ->where('scheduled_date', '>', now())
                ->with(['service', 'trainer'])
                ->orderBy('scheduled_date', 'asc')
                ->take(3)
                ->get();

            $upcomingClasses = \App\Models\Booking::where('user_id', $user->id)
                ->whereHas('schedule', function ($q) {
                    $q->where('start_time', '>', now());
                })
                ->with(['schedule.trainer', 'schedule.package'])
                ->orderBy(
                    \App\Models\Schedule::select('start_time')
                        ->whereColumn('id', 'bookings.schedule_id')
                        ->take(1)
                )
                ->take(3)
                ->get();

            $recentProgress = \App\Models\TrainingProgress::where('member_id', $user->id)
                ->with('trainer')
                ->latest('date')
                ->take(3)
                ->get();

            $availablePackages = MembershipPacket::where('is_active', true)->orderBy('price', 'asc')->take(3)->get();

            // 5. Assigned Trainers
            $assignedTrainers = $user->assignedTrainers()->get();

            // 6. Logika Peringatan Membership yang Lebih Rapi
            $membershipWarning = null;
            if ($activeMembership) {
                $days = $activeMembership->getRemainingDays();
                if ($days <= 0) {
                    $membershipWarning = ['type' => 'expired', 'message' => 'Membership Anda telah berakhir.'];
                } elseif ($days <= 7) {
                    $membershipWarning = ['type' => 'warning', 'message' => "Membership Anda akan berakhir dalam {$days} hari."];
                }
            }

            // 6. Optimasi Query Chart Bulanan (dari 12 query per chart menjadi 1 query per chart)
            $startPeriod = now()->subMonths(11)->startOfMonth();

            $checkinsData = CheckinLog::where('user_id', $user->id)
                ->where('checkin_time', '>=', $startPeriod)
                ->select(DB::raw("DATE_FORMAT(checkin_time, '%b %Y') as month_year"), DB::raw('COUNT(*) as count'))
                ->groupBy('month_year')
                ->get()->pluck('count', 'month_year');

            $monthlyCheckins = [];
            for ($i = 11; $i >= 0; $i--) {
                $monthKey = now()->subMonths($i)->format('M Y');
                $monthlyCheckins[$monthKey] = $checkinsData[$monthKey] ?? 0;
            }

            return [
                "user" => $user,
                "activeMembership" => $activeMembership,
                "activePromos" => $activePromos,
                "checkinStats" => $checkinStats,
                "recentTransactions" => $recentTransactions,
                "upcomingServices" => $upcomingServices,
                "upcomingClasses" => $upcomingClasses,
                "recentProgress" => $recentProgress,
                "membershipWarning" => $membershipWarning,
                "availablePackages" => $availablePackages,
                "monthlyCheckins" => $monthlyCheckins,
                "assignedTrainers" => $assignedTrainers,
                // Hilangkan data yang tidak perlu ditampilkan di dashboard member
                "pesan" => ""
            ];

        } catch (Exception $e) {
            // Jika terjadi error (misal: tabel belum ada), tampilkan pesan ini
            // Ini akan memaksa developer untuk menjalankan migrasi
            return [
                "user" => Auth::user(),
                "activeMembership" => null,
                "activePromos" => collect(),
                "checkinStats" => ['today' => 0, 'this_week' => 0, 'this_month' => 0, 'total' => 0],
                "recentTransactions" => collect(),
                "upcomingServices" => collect(),
                "membershipWarning" => null,
                "availablePackages" => collect(),
                "monthlyCheckins" => [],
                "assignedTrainers" => collect(),
                "pesan" => "Beberapa data mungkin tidak tersedia. Silakan jalankan migrasi database. Error: " . $e->getMessage()
            ];
        }
    }
}