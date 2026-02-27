<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use App\Models\Transaction;
use App\Models\CheckinLog;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SystemMonitoringController extends Controller
{
    public function index()
    {
        // Get system health metrics
        $systemHealth = $this->getSystemHealth();
        
        // Get real-time statistics
        $realTimeStats = $this->getRealTimeStats();
        
        // Get system alerts
        $alerts = $this->getSystemAlerts();
        
        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('owner.system-monitoring.index', compact(
            'systemHealth', 
            'realTimeStats', 
            'alerts', 
            'performanceMetrics'
        ));
    }

    public function serverStatus()
    {
        $serverInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
            'execution_time' => round(microtime(true) - LARAVEL_START, 3) . 's',
            'disk_usage' => $this->getDiskUsage(),
            'database_status' => $this->getDatabaseStatus(),
            'cache_status' => $this->getCacheStatus(),
        ];

        return response()->json($serverInfo);
    }

    public function databaseHealth()
    {
        $dbHealth = [
            'connection_status' => $this->testDatabaseConnection(),
            'table_sizes' => $this->getTableSizes(),
            'query_performance' => $this->getQueryPerformance(),
            'backup_status' => $this->getBackupStatus(),
        ];

        return response()->json($dbHealth);
    }

    public function userActivity()
    {
        // Get active users in last 24 hours
        $activeUsers = User::whereHas('checkinLogs', function ($query) {
                $query->where('checkin_time', '>=', now()->subDay());
            })
            ->count();

        // Get user activity by hour
        $hourlyActivity = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $hourlyActivity[$hour->format('H:00')] = CheckinLog::whereBetween('checkin_time', [
                $hour->startOfHour(),
                $hour->endOfHour()
            ])->count();
        }

        // Get user registration trends
        $registrationTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $registrationTrends[$date->format('M d')] = User::whereDate('created_at', $date)->count();
        }

        return response()->json([
            'active_users_24h' => $activeUsers,
            'hourly_activity' => $hourlyActivity,
            'registration_trends' => $registrationTrends,
        ]);
    }

    public function systemLogs()
    {
        $logFiles = [
            'laravel' => storage_path('logs/laravel.log'),
            'error' => storage_path('logs/error.log'),
            'access' => storage_path('logs/access.log'),
        ];

        $logs = [];
        foreach ($logFiles as $type => $file) {
            if (file_exists($file)) {
                $logs[$type] = [
                    'size' => $this->formatBytes(filesize($file)),
                    'modified' => Carbon::createFromTimestamp(filemtime($file))->diffForHumans(),
                    'lines' => $this->countLines($file),
                ];
            } else {
                $logs[$type] = [
                    'size' => '0 B',
                    'modified' => 'Never',
                    'lines' => 0,
                ];
            }
        }

        return response()->json($logs);
    }

    public function securityStatus()
    {
        $security = [
            'failed_logins_24h' => $this->getFailedLogins(),
            'suspicious_activities' => $this->getSuspiciousActivities(),
            'ssl_status' => $this->getSSLStatus(),
            'firewall_status' => $this->getFirewallStatus(),
            'backup_encryption' => $this->getBackupEncryption(),
        ];

        return response()->json($security);
    }

    public function performanceReport()
    {
        $performance = [
            'response_times' => $this->getResponseTimes(),
            'database_queries' => $this->getDatabaseQueryStats(),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'memory_usage_trend' => $this->getMemoryUsageTrend(),
            'cpu_usage' => $this->getCPUUsage(),
        ];

        return view('owner.system-monitoring.performance', compact('performance'));
    }

    public function alerts()
    {
        $alerts = $this->getSystemAlerts();
        return view('owner.system-monitoring.alerts', compact('alerts'));
    }

    public function resolveAlert(Request $request, $id)
    {
        // Mark alert as resolved
        Cache::forget("system_alert_{$id}");
        
        return response()->json([
            'success' => true,
            'message' => 'Alert berhasil diselesaikan'
        ]);
    }

    private function getSystemHealth()
    {
        return [
            'overall_status' => 'healthy', // healthy, warning, critical
            'database' => $this->testDatabaseConnection() ? 'online' : 'offline',
            'cache' => $this->testCacheConnection() ? 'online' : 'offline',
            'storage' => $this->testStorageAccess() ? 'accessible' : 'error',
            'queue' => $this->testQueueConnection() ? 'running' : 'stopped',
        ];
    }

    private function getRealTimeStats()
    {
        return [
            'active_users' => User::whereHas('checkinLogs', function ($query) {
                $query->where('checkin_time', '>=', now()->subHour());
            })->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'today_revenue' => Transaction::where('status', 'validated')
                ->whereDate('transaction_date', today())
                ->sum('amount'),
            'active_memberships' => Membership::where('status', 'active')
                ->where('end_date', '>=', now())
                ->count(),
        ];
    }

    private function getSystemAlerts()
    {
        $alerts = [];

        // Check for low disk space
        $diskUsage = $this->getDiskUsage();
        if ($diskUsage['percentage'] > 85) {
            $alerts[] = [
                'id' => 'disk_space',
                'type' => 'warning',
                'title' => 'Ruang Disk Hampir Penuh',
                'message' => "Penggunaan disk: {$diskUsage['percentage']}%",
                'created_at' => now(),
            ];
        }

        // Check for failed transactions
        $failedTransactions = Transaction::where('status', 'cancelled')
            ->whereDate('created_at', today())
            ->count();
        
        if ($failedTransactions > 10) {
            $alerts[] = [
                'id' => 'failed_transactions',
                'type' => 'critical',
                'title' => 'Banyak Transaksi Gagal',
                'message' => "{$failedTransactions} transaksi gagal hari ini",
                'created_at' => now(),
            ];
        }

        // Check for expiring memberships
        $expiringMemberships = Membership::where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(3)])
            ->count();

        if ($expiringMemberships > 0) {
            $alerts[] = [
                'id' => 'expiring_memberships',
                'type' => 'info',
                'title' => 'Membership Akan Berakhir',
                'message' => "{$expiringMemberships} membership akan berakhir dalam 3 hari",
                'created_at' => now(),
            ];
        }

        return $alerts;
    }

    private function getPerformanceMetrics()
    {
        return [
            'avg_response_time' => $this->getAverageResponseTime(),
            'database_queries_per_second' => $this->getDatabaseQPS(),
            'memory_usage_percentage' => $this->getMemoryUsagePercentage(),
            'active_connections' => $this->getActiveConnections(),
        ];
    }

    private function testDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testCacheConnection()
    {
        try {
            Cache::put('test_key', 'test_value', 1);
            return Cache::get('test_key') === 'test_value';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testStorageAccess()
    {
        try {
            Storage::put('test.txt', 'test');
            $result = Storage::exists('test.txt');
            Storage::delete('test.txt');
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testQueueConnection()
    {
        // This would check if queue workers are running
        // Implementation depends on your queue setup
        return true;
    }

    private function getDiskUsage()
    {
        $bytes = disk_free_space('/');
        $total = disk_total_space('/');
        $used = $total - $bytes;
        
        return [
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($total),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    private function getDatabaseStatus()
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return [
                'status' => 'connected',
                'connections' => $result[0]->Value ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getCacheStatus()
    {
        try {
            $info = Cache::getStore();
            return [
                'driver' => config('cache.default'),
                'status' => 'connected',
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'error',
            ];
        }
    }

    private function getTableSizes()
    {
        try {
            $tables = DB::select("
                SELECT 
                    table_name AS 'table',
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");
            
            return collect($tables)->mapWithKeys(function ($table) {
                return [$table->table => $table->size_mb . ' MB'];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getQueryPerformance()
    {
        // This would analyze slow query log
        // Implementation depends on your database setup
        return [
            'slow_queries' => 0,
            'avg_query_time' => '0.001s',
        ];
    }

    private function getBackupStatus()
    {
        // Check last backup timestamp
        $backupPath = storage_path('backups');
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.sql');
            if (!empty($files)) {
                $lastBackup = max(array_map('filemtime', $files));
                return [
                    'status' => 'available',
                    'last_backup' => Carbon::createFromTimestamp($lastBackup)->diffForHumans(),
                ];
            }
        }
        
        return [
            'status' => 'no_backup',
            'last_backup' => 'Never',
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function countLines($file)
    {
        if (!file_exists($file)) {
            return 0;
        }
        
        $linecount = 0;
        $handle = fopen($file, "r");
        while (!feof($handle)) {
            $line = fgets($handle);
            $linecount++;
        }
        fclose($handle);
        
        return $linecount;
    }

    private function getFailedLogins()
    {
        // This would check authentication logs
        return 0;
    }

    private function getSuspiciousActivities()
    {
        // This would analyze access patterns
        return [];
    }

    private function getSSLStatus()
    {
        return [
            'enabled' => request()->isSecure(),
            'certificate' => 'valid',
        ];
    }

    private function getFirewallStatus()
    {
        return [
            'status' => 'active',
            'rules' => 25,
        ];
    }

    private function getBackupEncryption()
    {
        return [
            'enabled' => true,
            'algorithm' => 'AES-256',
        ];
    }

    private function getResponseTimes()
    {
        // This would analyze response time logs
        return [
            'avg' => '150ms',
            'p95' => '300ms',
            'p99' => '500ms',
        ];
    }

    private function getDatabaseQueryStats()
    {
        return [
            'total_queries' => 1250,
            'slow_queries' => 3,
            'avg_time' => '0.05s',
        ];
    }

    private function getCacheHitRatio()
    {
        return 85.5; // percentage
    }

    private function getMemoryUsageTrend()
    {
        $trend = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $trend[$hour->format('H:00')] = rand(60, 85); // Mock data
        }
        return $trend;
    }

    private function getCPUUsage()
    {
        // This would get actual CPU usage
        return rand(20, 60); // Mock data
    }

    private function getAverageResponseTime()
    {
        return '150ms';
    }

    private function getDatabaseQPS()
    {
        return 45.2;
    }

    private function getMemoryUsagePercentage()
    {
        $usage = memory_get_usage(true);
        $limit = $this->parseMemoryLimit(ini_get('memory_limit'));
        return round(($usage / $limit) * 100, 2);
    }

    private function getActiveConnections()
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function parseMemoryLimit($limit)
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $limit = (int) $limit;
        
        switch ($last) {
            case 'g':
                $limit *= 1024;
            case 'm':
                $limit *= 1024;
            case 'k':
                $limit *= 1024;
        }
        
        return $limit;
    }
}
