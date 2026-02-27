@extends('layouts.app')
@section('css')
<style>
.business-card {
    transition: all 0.3s ease;
    border-left: 4px solid;
}
.business-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.growth-positive {
    color: #10b981 !important;
}
.growth-negative {
    color: #ef4444 !important;
}
.activity-item {
    transition: all 0.2s ease;
}
.activity-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
}
.chart-container {
    position: relative;
    height: 300px;
}
/* Tambahan untuk background biru tua */
.bg-blue-dark {
    background-color: #1e3a8a !important;
}
</style>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    
    <!-- Header Executive Summary -->
    <div class="row mb-6">
        <div class="col-12">
            <div class="card bg-blue-dark">
                <div class="card-body text-center py-8">
                    <h1 class="text-white fw-bold mb-3">
                        <i class="fas fa-crown me-3"></i>Dashboard  {{ $data['user']->name }}
                    </h1>
                    <p class="text-white opacity-75 fs-5 mb-4">
                        Monitoring Bisnis & Analisis Performa Gym Bugar Sehat
                    </p>
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="text-white">
                                <div class="fs-2x fw-bold">{{ $data['totalMembers'] }}</div>
                                <div class="opacity-75">Total Member</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-white">
                                <div class="fs-2x fw-bold">Rp {{ number_format($data['monthlyRevenue'], 0, ',', '.') }}</div>
                                <div class="opacity-75">Pendapatan Bulan Ini</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-white">
                                <div class="fs-2x fw-bold {{ $data['growthRate'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                    {{ $data['growthRate'] >= 0 ? '+' : '' }}{{ number_format($data['growthRate'], 1) }}%
                                </div>
                                <div class="opacity-75">Growth Rate</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-white">
                                <div class="fs-2x fw-bold">{{ $data['todayCheckins'] }}</div>
                                <div class="opacity-75">Check-in Hari Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(count($data['alerts']) > 0)
    <div class="row mb-6">
        @foreach($data['alerts'] as $alert)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center p-4">
                <i class="fas fa-{{ $alert['type'] == 'success' ? 'chart-line' : ($alert['type'] == 'danger' ? 'exclamation-triangle' : 'info-circle') }} fs-2x me-4"></i>
                <div>
                    <h5 class="mb-1">{{ $alert['title'] }}</h5>
                    <div class="fs-7">{{ $alert['message'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Business Overview Cards -->
    <div class="row g-5 g-xl-6 mb-6">
        <div class="col-6 col-lg-3">
            <div class="card business-card border-primary">
                <div class="card-body text-center py-6">
                    <i class="fas fa-users fs-2x text-primary mb-3"></i>
                    <div class="fs-2x fw-bold text-primary">{{ $data['totalActiveMemberships'] }}</div>
                    <div class="text-gray-600 fs-7">Membership Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card business-card border-success">
                <div class="card-body text-center py-6">
                    <i class="fas fa-chart-line fs-2x text-success mb-3"></i>
                    <div class="fs-2x fw-bold text-success">Rp {{ number_format($data['yearlyRevenue'], 0, ',', '.') }}</div>
                    <div class="text-gray-600 fs-7">Pendapatan Tahun Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card business-card border-info">
                <div class="card-body text-center py-6">
                    <i class="fas fa-user-friends fs-2x text-info mb-3"></i>
                    <div class="fs-2x fw-bold text-info">{{ $data['totalStaff'] }}</div>
                    <div class="text-gray-600 fs-7">Total Staff</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card business-card border-warning">
                <div class="card-body text-center py-6">
                    <i class="fas fa-calendar-check fs-2x text-warning mb-3"></i>
                    <div class="fs-2x fw-bold text-warning">{{ number_format($data['avgDailyCheckins'], 1) }}</div>
                    <div class="text-gray-600 fs-7">Rata-rata Check-in/Hari</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Row -->
    <div class="row g-5 g-xl-6">
        
        <!-- Pendapatan 12 Bulan -->
        <div class="col-12 col-lg-8">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area me-2"></i>Trend Pendapatan 12 Bulan Terakhir
                    </h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-success fs-7">
                            Total: Rp {{ number_format(array_sum($data['monthlyRevenueChart']), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Distribution -->
        <div class="col-12 col-lg-4">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi Membership
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['membershipByPackage']->count() > 0)
                        @foreach($data['membershipByPackage'] as $package)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary text-primary">
                                    {{ substr($package->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $package->name }}</div>
                                <div class="text-muted fs-7">
                                    Aktif: {{ $package->active_count }} | Total: {{ $package->total_count }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-6 fw-bold">{{ $package->active_count }}</div>
                                <div class="progress h-5px w-50px">
                                    <div class="progress-bar bg-primary" style="width: {{ $data['totalActiveMemberships'] > 0 ? ($package->active_count / $data['totalActiveMemberships']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fs-2x text-gray-400 mb-3"></i>
                            <div class="text-gray-600">Belum ada data membership</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Member Growth Chart -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus me-2"></i>Pertumbuhan Member Baru
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyMembersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Check-ins -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Check-in 30 Hari Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="dailyCheckinsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="col-12 col-lg-4">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>Breakdown Pendapatan Bulan Ini
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-40px me-4">
                            <span class="symbol-label bg-light-primary text-primary">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Membership</div>
                            <div class="text-muted fs-7">Penjualan paket membership</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-6 fw-bold text-primary">Rp {{ number_format($data['membershipRevenue'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-40px me-4">
                            <span class="symbol-label bg-light-success text-success">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Produk</div>
                            <div class="text-muted fs-7">Penjualan produk gym</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-6 fw-bold text-success">Rp {{ number_format($data['productRevenue'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-40px me-4">
                            <span class="symbol-label bg-light-info text-info">
                                <i class="fas fa-dumbbell"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Layanan</div>
                            <div class="text-muted fs-7">Personal training, dll</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-6 fw-bold text-info">Rp {{ number_format($data['serviceRevenue'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    
                    <div class="separator my-4"></div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-5">Total Pendapatan</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-4 fw-bold text-primary">Rp {{ number_format($data['monthlyRevenue'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-12 col-lg-4">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['recentActivities']->count() > 0)
                        @foreach($data['recentActivities'] as $activity)
                        <div class="activity-item d-flex align-items-center mb-4 p-2">
                            <div class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-{{ $activity['color'] }} text-{{ $activity['color'] }}">
                                    <i class="{{ $activity['icon'] }} fs-7"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold fs-7">{{ $activity['title'] }}</div>
                                <div class="text-muted fs-8">{{ $activity['description'] }}</div>
                                <div class="text-muted fs-8">{{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fs-2x text-gray-400 mb-3"></i>
                            <div class="text-gray-600">Belum ada aktivitas</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Stats -->
        <div class="col-12 col-lg-4">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database me-2"></i>Statistik Sistem
                    </h3>
                </div>
                <div class="card-body">
                    @foreach($data['dbStats'] as $table => $count)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $table) }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-light-primary">{{ number_format($count) }}</span>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="separator my-4"></div>
                    
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold">Total Records</div>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-primary">{{ number_format(array_sum($data['dbStats'])) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Pendapatan Bulanan
const monthlyRevenueData = @json($data['monthlyRevenueChart'] ?? []);
const revenueCtx = document.getElementById('monthlyRevenueChart');
if (revenueCtx && monthlyRevenueData) {
new Chart(revenueCtx.getContext('2d'), {
    type: 'line',
    data: {
        labels: Object.keys(monthlyRevenueData),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: Object.values(monthlyRevenueData),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    }
                }
            }
        }
    }
});
}

// Chart Member Baru
const monthlyMembersData = @json($data['monthlyMembersChart'] ?? []);
const membersCtx = document.getElementById('monthlyMembersChart');
if (membersCtx && monthlyMembersData) {
new Chart(membersCtx.getContext('2d'), {
    type: 'bar',
    data: {
        labels: Object.keys(monthlyMembersData),
        datasets: [{
            label: 'Member Baru',
            data: Object.values(monthlyMembersData),
            backgroundColor: '#10b981',
            borderColor: '#059669',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
}

// Chart Check-in Harian
const dailyCheckinsData = @json($data['dailyCheckinsChart'] ?? []);
const checkinsCtx = document.getElementById('dailyCheckinsChart');
if (checkinsCtx && dailyCheckinsData) {
new Chart(checkinsCtx.getContext('2d'), {
    type: 'line',
    data: {
        labels: Object.keys(dailyCheckinsData),
        datasets: [{
            label: 'Check-in',
            data: Object.values(dailyCheckinsData),
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                ticks: {
                    maxTicksLimit: 10
                }
            }
        }
    }
});
}

// Auto refresh setiap 10 menit
setInterval(function() {
    location.reload();
}, 600000);
</script>
@endsection
