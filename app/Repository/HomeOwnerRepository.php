<?php
namespace App\Repository;

use App\Models\User;
use App\Models\Membership;
use App\Models\CheckinLog;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\ServiceTransaction;
use App\Models\MembershipPacket;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class HomeOwnerRepository{
    
    public static function generate($request=[]){
        try {
            $user = Auth::user();
            
            // === OVERVIEW BISNIS ===
            
            // Total Users berdasarkan role
            $totalMembers = User::whereHas('roles', function($query) {
                $query->where('name', 'User:Member');
            })->count();
            
            $totalStaff = User::whereHas('roles', function($query) {
                $query->where('name', 'User:Staff');
            })->count();
            
            $totalOwners = User::whereHas('roles', function($query) {
                $query->where('name', 'User:Owner');
            })->count();
            
            // Total Membership Aktif
            $totalActiveMemberships = Membership::where('status', 'active')
                ->where('end_date', '>=', Carbon::now()->toDateString())
                ->count();
            
            // Total Pendapatan Bulan Ini
            $monthlyRevenue = Transaction::whereMonth('transaction_date', Carbon::now()->month)
                ->whereYear('transaction_date', Carbon::now()->year)
                ->where('status', 'validated')
                ->sum('amount') ?? 0;
            
            // Total Pendapatan Tahun Ini
            $yearlyRevenue = Transaction::whereYear('transaction_date', Carbon::now()->year)
                ->where('status', 'validated')
                ->sum('amount') ?? 0;
            
            // Growth Rate (perbandingan bulan ini vs bulan lalu)
            $lastMonthRevenue = Transaction::whereMonth('transaction_date', Carbon::now()->subMonth()->month)
                ->whereYear('transaction_date', Carbon::now()->subMonth()->year)
                ->where('status', 'validated')
                ->sum('amount') ?? 0;
            
            $growthRate = 0;
            if ($lastMonthRevenue > 0) {
                $growthRate = (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            }
            
            // === STATISTIK OPERASIONAL ===
            
            // Check-in hari ini
            $todayCheckins = CheckinLog::whereDate('checkin_time', Carbon::today())->count();
            
            // Check-in bulan ini
            $monthlyCheckins = CheckinLog::whereMonth('checkin_time', Carbon::now()->month)
                ->whereYear('checkin_time', Carbon::now()->year)
                ->count();
            
            // Rata-rata check-in per hari bulan ini
            $daysInMonth = Carbon::now()->daysInMonth;
            $avgDailyCheckins = $daysInMonth > 0 ? round($monthlyCheckins / $daysInMonth, 1) : 0;
            
            // === ANALISIS MEMBERSHIP ===
            
            // Membership berdasarkan paket
            $membershipByPackage = MembershipPacket::withCount([
                'memberships as active_count' => function($query) {
                    $query->where('status', 'active')
                          ->where('end_date', '>=', Carbon::now());
                },
                'memberships as total_count'
            ])->get();
            
            // Membership yang akan expired dalam 30 hari
            $expiringMemberships = Membership::where('status', 'active')
                ->whereBetween('end_date', [
                    Carbon::now(),
                    Carbon::now()->addDays(30)
                ])
                ->count();
            
            // Retention rate (member yang memperpanjang)
            $renewedMemberships = Membership::where('type', 'renewal')
                ->whereMonth('start_date', Carbon::now()->month)
                ->count();
            
            $newMemberships = Membership::where('type', 'new')
                ->whereMonth('start_date', Carbon::now()->month)
                ->count();
            
            // === ANALISIS KEUANGAN ===
            
            // Pendapatan per kategori
            $membershipRevenue = DB::table('transactions')
                ->join('membership_packages', 'transactions.product_id', '=', 'membership_packages.id')
                ->whereMonth('transactions.transaction_date', Carbon::now()->month)
                ->where('transactions.status', 'validated')
                ->sum('transactions.amount') ?? 0;
            
            $productRevenue = Transaction::whereHas('product')
                ->whereMonth('transaction_date', Carbon::now()->month)
                ->where('status', 'validated')
                ->sum('amount') ?? 0;
            
            $serviceRevenue = ServiceTransaction::whereMonth('transaction_date', Carbon::now()->month)
                ->where('status', 'completed')
                ->sum('amount') ?? 0;
            
            // Top 5 produk terlaris
            $topProducts = Product::withCount(['transactions' => function($query) {
                $query->whereMonth('transaction_date', Carbon::now()->month)
                      ->where('status', 'validated');
            }])
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();
            
            // === GRAFIK & CHART DATA ===
            
            // Pendapatan 12 bulan terakhir
            $monthlyRevenueChart = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthKey = $month->format('M Y');
                $monthlyRevenueChart[$monthKey] = Transaction::whereMonth('transaction_date', $month->month)
                    ->whereYear('transaction_date', $month->year)
                    ->where('status', 'validated')
                    ->sum('amount') ?? 0;
            }
            
            // Member baru 12 bulan terakhir
            $monthlyMembersChart = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthKey = $month->format('M Y');
                $monthlyMembersChart[$monthKey] = Membership::whereMonth('start_date', $month->month)
                    ->whereYear('start_date', $month->year)
                    ->count();
            }
            
            // Check-in 30 hari terakhir
            $dailyCheckinsChart = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateKey = $date->format('d M');
                $dailyCheckinsChart[$dateKey] = CheckinLog::whereDate('checkin_time', $date)
                    ->count();
            }
            
            // === SISTEM MONITORING ===
            
            // Database size (approximate)
            $dbStats = [
                'users' => User::count(),
                'memberships' => Membership::count(),
                'transactions' => Transaction::count(),
                'checkins' => CheckinLog::count(),
                'products' => Product::count(),
                'services' => Service::count()
            ];
            
            // Recent activities (gabungan dari berbagai tabel)
            $recentActivities = collect();
            
            // Recent memberships
            $recentMemberships = Membership::with(['user', 'package'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'membership',
                        'title' => 'Membership Baru',
                        'description' => $item->user->name . ' - ' . $item->package->name,
                        'time' => $item->created_at,
                        'icon' => 'fas fa-id-card',
                        'color' => 'primary'
                    ];
                });
            
            // Recent transactions
            $recentTransactions = Transaction::with(['user', 'product'])
                ->where('status', 'validated')
                ->orderBy('transaction_date', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'transaction',
                        'title' => 'Transaksi',
                        'description' => $item->user->name . ' - Rp ' . number_format($item->amount, 0, ',', '.'),
                        'time' => $item->transaction_date,
                        'icon' => 'fas fa-money-bill',
                        'color' => 'success'
                    ];
                });
            
            $recentActivities = $recentMemberships->concat($recentTransactions)
                ->sortByDesc('time')
                ->take(10);
            
            // === ALERTS & NOTIFICATIONS ===
            
            $alerts = [];
            
            // Alert membership akan expired
            if ($expiringMemberships > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Membership Akan Berakhir',
                    'message' => $expiringMemberships . ' membership akan berakhir dalam 30 hari',
                    'count' => $expiringMemberships,
                    'action' => 'Lihat Detail'
                ];
            }
            
            // Alert pertumbuhan negatif
            if ($growthRate < -10) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Penurunan Pendapatan',
                    'message' => 'Pendapatan turun ' . abs(round($growthRate, 1)) . '% dari bulan lalu',
                    'count' => abs(round($growthRate, 1)),
                    'action' => 'Analisis'
                ];
            }
            
            // Alert pertumbuhan positif
            if ($growthRate > 10) {
                $alerts[] = [
                    'type' => 'success',
                    'title' => 'Pertumbuhan Bagus',
                    'message' => 'Pendapatan naik ' . round($growthRate, 1) . '% dari bulan lalu',
                    'count' => round($growthRate, 1),
                    'action' => 'Lihat Trend'
                ];
            }
            
            // Alert check-in rendah
            if ($todayCheckins < ($avgDailyCheckins * 0.7)) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Check-in Rendah',
                    'message' => 'Check-in hari ini di bawah rata-rata',
                    'count' => $todayCheckins,
                    'action' => 'Monitor'
                ];
            }
            
            $result = [
                // Overview Bisnis
                "totalMembers" => $totalMembers,
                "totalStaff" => $totalStaff,
                "totalOwners" => $totalOwners,
                "totalActiveMemberships" => $totalActiveMemberships,
                "monthlyRevenue" => $monthlyRevenue,
                "yearlyRevenue" => $yearlyRevenue,
                "growthRate" => $growthRate,
                
                // Statistik Operasional
                "todayCheckins" => $todayCheckins,
                "monthlyCheckins" => $monthlyCheckins,
                "avgDailyCheckins" => $avgDailyCheckins,
                
                // Analisis Membership
                "membershipByPackage" => $membershipByPackage,
                "expiringMemberships" => $expiringMemberships,
                "renewedMemberships" => $renewedMemberships,
                "newMemberships" => $newMemberships,
                
                // Analisis Keuangan
                "membershipRevenue" => $membershipRevenue,
                "productRevenue" => $productRevenue,
                "serviceRevenue" => $serviceRevenue,
                "topProducts" => $topProducts,
                
                // Chart Data
                "monthlyRevenueChart" => $monthlyRevenueChart,
                "monthlyMembersChart" => $monthlyMembersChart,
                "dailyCheckinsChart" => $dailyCheckinsChart,
                
                // Sistem Monitoring
                "dbStats" => $dbStats,
                "recentActivities" => $recentActivities,
                
                // Alerts
                "alerts" => $alerts,
                
                "user" => $user,
                "pesan" => ""
            ];
            
            return $result;
            
        } catch (Exception $e) {
            // Fallback jika ada error
            return [
                "totalMembers" => 0,
                "totalStaff" => 0,
                "totalOwners" => 0,
                "totalActiveMemberships" => 0,
                "monthlyRevenue" => 0,
                "yearlyRevenue" => 0,
                "growthRate" => 0,
                "todayCheckins" => 0,
                "monthlyCheckins" => 0,
                "avgDailyCheckins" => 0,
                "membershipByPackage" => collect(),
                "expiringMemberships" => 0,
                "renewedMemberships" => 0,
                "newMemberships" => 0,
                "membershipRevenue" => 0,
                "productRevenue" => 0,
                "serviceRevenue" => 0,
                "topProducts" => collect(),
                "monthlyRevenueChart" => [],
                "monthlyMembersChart" => [],
                "dailyCheckinsChart" => [],
                "dbStats" => [],
                "recentActivities" => collect(),
                "alerts" => [],
                "user" => Auth::user(),
                "pesan" => "Beberapa data mungkin tidak tersedia. Silakan periksa koneksi database."
            ];
        }
    }
}
