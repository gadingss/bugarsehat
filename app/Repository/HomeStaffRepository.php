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

class HomeStaffRepository{
    
    public static function generate($request=[]){
        try {
            $user = Auth::user();
            
            // === STATISTIK UTAMA STAFF ===
            
            // Total Member Aktif
            $totalActiveMembers = Membership::where('status', 'active')
                ->where('end_date', '>=', Carbon::now()->toDateString())
                ->count();
            
            // Member Baru Hari Ini
            $newMembersToday = Membership::whereDate('start_date', Carbon::today())
                ->count();
            
            // Check-in Hari Ini
            $checkinsToday = CheckinLog::whereDate('checkin_time', Carbon::today())
                ->count();
            
            // Transaksi Hari Ini
            $transactionsToday = Transaction::whereDate('transaction_date', Carbon::today())
                ->where('status', 'validated')
                ->count();
            
            // Pendapatan Hari Ini
            $revenueToday = Transaction::whereDate('transaction_date', Carbon::today())
                ->where('status', 'validated')
                ->sum('amount') ?? 0;
            
            // === MEMBER MANAGEMENT ===
            
            // Member yang akan expired dalam 7 hari
            $expiringMembers = Membership::where('status', 'active')
                ->whereBetween('end_date', [
                    Carbon::now(),
                    Carbon::now()->addDays(7)
                ])
                ->with(['user', 'package'])
                ->orderBy('end_date', 'asc')
                ->take(10)
                ->get();
            
            // Member dengan kunjungan sedikit (kurang dari 3 kali remaining)
            $lowVisitMembers = Membership::where('status', 'active')
                ->where('remaining_visits', '<=', 3)
                ->where('remaining_visits', '>', 0)
                ->with(['user', 'package'])
                ->orderBy('remaining_visits', 'asc')
                ->take(10)
                ->get();
            
            // Check-in terbaru hari ini
            $recentCheckins = CheckinLog::whereDate('checkin_time', Carbon::today())
                ->with(['user', 'membership.package'])
                ->orderBy('checkin_time', 'desc')
                ->take(10)
                ->get();
            
            // === TRANSAKSI & AKTIVASI ===
            
            // Membership pending aktivasi
            $pendingActivations = Membership::where('status', 'pending')
                ->with(['user', 'package'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Transaksi pending validasi
            $pendingTransactions = Transaction::where('status', 'pending')
                ->with(['user', 'product'])
                ->orderBy('transaction_date', 'desc')
                ->take(10)
                ->get();
            
            // === PRODUK & LAYANAN ===
            
            // Produk dengan stok rendah
            $lowStockProducts = collect();
            try {
                if (Schema::hasColumn('products', 'stock') && Schema::hasColumn('products', 'min_stock')) {
                    $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
                        ->where('is_active', true)
                        ->orderBy('stock', 'asc')
                        ->take(10)
                        ->get();
                }
            } catch (Exception $e) {
                // Fallback jika kolom tidak ada
                $lowStockProducts = collect();
            }
            
            // Layanan yang dijadwalkan hari ini
            $todayServices = ServiceTransaction::whereDate('scheduled_date', Carbon::today())
                ->where('status', 'scheduled')
                ->with(['user', 'service'])
                ->orderBy('scheduled_date', 'asc')
                ->get();
            
            // === LAPORAN HARIAN ===
            
            // Statistik check-in per jam hari ini
            $hourlyCheckins = [];
            for ($hour = 6; $hour <= 22; $hour++) {
                $hourlyCheckins[$hour . ':00'] = CheckinLog::whereDate('checkin_time', Carbon::today())
                    ->whereTime('checkin_time', '>=', sprintf('%02d:00:00', $hour))
                    ->whereTime('checkin_time', '<', sprintf('%02d:00:00', $hour + 1))
                    ->count();
            }
            
            // Statistik membership per paket
            $membershipStats = MembershipPacket::withCount([
                'memberships as active_count' => function($query) {
                    $query->where('status', 'active')
                          ->where('end_date', '>=', Carbon::now());
                }
            ])->get();
            
            // === GRAFIK MINGGUAN ===
            
            // Check-in 7 hari terakhir
            $weeklyCheckins = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $weeklyCheckins[$date->format('D')] = CheckinLog::whereDate('checkin_time', $date)
                    ->count();
            }
            
            // Pendapatan 7 hari terakhir
            $weeklyRevenue = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $weeklyRevenue[$date->format('D')] = Transaction::whereDate('transaction_date', $date)
                    ->where('status', 'validated')
                    ->sum('amount') ?? 0;
            }
            
            // Member baru 7 hari terakhir
            $weeklyNewMembers = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $weeklyNewMembers[$date->format('D')] = Membership::whereDate('start_date', $date)
                    ->count();
            }
            
            // === ALERT & NOTIFIKASI ===
            
            $alerts = [];
            
            // Alert membership expired
            if ($expiringMembers->count() > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Membership Akan Berakhir',
                    'message' => $expiringMembers->count() . ' membership akan berakhir dalam 7 hari',
                    'count' => $expiringMembers->count()
                ];
            }
            
            // Alert kunjungan rendah
            if ($lowVisitMembers->count() > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Kunjungan Rendah',
                    'message' => $lowVisitMembers->count() . ' member dengan sisa kunjungan sedikit',
                    'count' => $lowVisitMembers->count()
                ];
            }
            
            // Alert pending aktivasi
            if ($pendingActivations->count() > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Pending Aktivasi',
                    'message' => $pendingActivations->count() . ' membership menunggu aktivasi',
                    'count' => $pendingActivations->count()
                ];
            }
            
            // Alert stok rendah
            if ($lowStockProducts->count() > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Stok Rendah',
                    'message' => $lowStockProducts->count() . ' produk dengan stok rendah',
                    'count' => $lowStockProducts->count()
                ];
            }
            
            $result = [
                // Statistik Utama
                "totalActiveMembers" => $totalActiveMembers,
                "newMembersToday" => $newMembersToday,
                "checkinsToday" => $checkinsToday,
                "transactionsToday" => $transactionsToday,
                "revenueToday" => $revenueToday,
                
                // Member Management
                "expiringMembers" => $expiringMembers,
                "lowVisitMembers" => $lowVisitMembers,
                "recentCheckins" => $recentCheckins,
                
                // Transaksi & Aktivasi
                "pendingActivations" => $pendingActivations,
                "pendingTransactions" => $pendingTransactions,
                
                // Produk & Layanan
                "lowStockProducts" => $lowStockProducts,
                "todayServices" => $todayServices,
                
                // Laporan
                "hourlyCheckins" => $hourlyCheckins,
                "membershipStats" => $membershipStats,
                
                // Grafik
                "weeklyCheckins" => $weeklyCheckins,
                "weeklyRevenue" => $weeklyRevenue,
                "weeklyNewMembers" => $weeklyNewMembers,
                
                // Alert
                "alerts" => $alerts,
                
                "user" => $user,
                "pesan" => ""
            ];
            
            return $result;
            
        } catch (Exception $e) {
            // Fallback jika ada error
            return [
                "totalActiveMembers" => 0,
                "newMembersToday" => 0,
                "checkinsToday" => 0,
                "transactionsToday" => 0,
                "revenueToday" => 0,
                "expiringMembers" => collect(),
                "lowVisitMembers" => collect(),
                "recentCheckins" => collect(),
                "pendingActivations" => collect(),
                "pendingTransactions" => collect(),
                "lowStockProducts" => collect(),
                "todayServices" => collect(),
                "hourlyCheckins" => [],
                "membershipStats" => collect(),
                "weeklyCheckins" => [],
                "weeklyRevenue" => [],
                "weeklyNewMembers" => [],
                "alerts" => [],
                "user" => Auth::user(),
                "pesan" => "Beberapa data mungkin tidak tersedia. Silakan periksa koneksi database."
            ];
        }
    }
}
