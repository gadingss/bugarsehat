@extends('layouts.app')
@section('css')
<style>
.alert-card {
    border-left: 4px solid;
    transition: all 0.3s ease;
}
.alert-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.stat-card {
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    
    <!-- Header Welcome -->
    <div class="row mb-6">
        <div class="col-12">
            <div class="card bg-primary">
                <div class="card-body text-center py-8">
                    <h1 class="text-white fw-bold mb-3">
                        <i class="fas fa-user-tie me-3"></i>Dashboard {{ $data['user']->name }}
                    </h1>
                    <p class="text-white opacity-75 fs-5 mb-0">
                        Kelola member, transaksi, dan operasional gym hari ini
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(count($data['alerts']) > 0)
    <div class="row mb-6">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center p-5">
                <i class="fas fa-bell fs-2x text-info me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-info">Notifikasi Penting</h4>
                    <span>Ada {{ count($data['alerts']) }} hal yang perlu perhatian Anda</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistik Utama -->
    <div class="row g-5 g-xl-6 mb-6">
        <div class="col-6 col-lg-3">
            <div class="card stat-card bg-light-primary">
                <div class="card-body text-center py-6">
                    <i class="fas fa-users fs-2x text-primary mb-3"></i>
                    <div class="fs-2x fw-bold text-primary">{{ $data['totalActiveMembers'] }}</div>
                    <div class="text-gray-600 fs-7">Member Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card bg-light-success">
                <div class="card-body text-center py-6">
                    <i class="fas fa-user-plus fs-2x text-success mb-3"></i>
                    <div class="fs-2x fw-bold text-success">{{ $data['newMembersToday'] }}</div>
                    <div class="text-gray-600 fs-7">Member Baru Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card bg-light-info">
                <div class="card-body text-center py-6">
                    <i class="fas fa-sign-in-alt fs-2x text-info mb-3"></i>
                    <div class="fs-2x fw-bold text-info">{{ $data['checkinsToday'] }}</div>
                    <div class="text-gray-600 fs-7">Check-in Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card bg-light-warning">
                <div class="card-body text-center py-6">
                    <i class="fas fa-money-bill-wave fs-2x text-warning mb-3"></i>
                    <div class="fs-2x fw-bold text-warning">Rp {{ number_format($data['revenueToday'], 0, ',', '.') }}</div>
                    <div class="text-gray-600 fs-7">Pendapatan Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Cards -->
    @if(count($data['alerts']) > 0)
    <div class="row g-5 mb-6">
        @foreach($data['alerts'] as $alert)
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card alert-card border-{{ $alert['type'] == 'danger' ? 'danger' : ($alert['type'] == 'warning' ? 'warning' : 'info') }}">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ $alert['type'] == 'danger' ? 'exclamation-triangle' : ($alert['type'] == 'warning' ? 'exclamation-circle' : 'info-circle') }} fs-2x text-{{ $alert['type'] == 'danger' ? 'danger' : ($alert['type'] == 'warning' ? 'warning' : 'info') }} me-3"></i>
                        <div>
                            <div class="fw-bold text-{{ $alert['type'] == 'danger' ? 'danger' : ($alert['type'] == 'warning' ? 'warning' : 'info') }}">{{ $alert['title'] }}</div>
                            <div class="text-gray-600 fs-7">{{ $alert['message'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Main Content Row -->
    <div class="row g-5 g-xl-6">
        
        <!-- Check-in Terbaru -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>Check-in Terbaru Hari Ini
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['recentCheckins']->count() > 0)
                        @foreach($data['recentCheckins'] as $checkin)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary text-primary">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $checkin->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted fs-7">
                                    {{ \Carbon\Carbon::parse($checkin->checkin_time)->format('H:i') }} - 
                                    {{ $checkin->membership->package->name ?? 'No Package' }}
                                </div>
                            </div>
                            <span class="badge badge-light-success">Aktif</span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fs-2x text-gray-400 mb-3"></i>
                            <div class="text-gray-600">Belum ada check-in hari ini</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Member Akan Expired -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Member Akan Expired
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['expiringMembers']->count() > 0)
                        @foreach($data['expiringMembers'] as $member)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-warning text-warning">
                                    <i class="fas fa-user-clock"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $member->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted fs-7">
                                    {{ $member->package->name ?? 'No Package' }} - 
                                    Berakhir: {{ \Carbon\Carbon::parse($member->end_date)->format('d M Y') }}
                                </div>
                            </div>
                            <span class="badge badge-warning">
                                {{ \Carbon\Carbon::parse($member->end_date)->diffInDays(\Carbon\Carbon::now()) }} hari
                            </span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fs-2x text-success mb-3"></i>
                            <div class="text-gray-600">Tidak ada member yang akan expired</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Aktivasi -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hourglass-half me-2 text-danger"></i>Pending Aktivasi
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['pendingActivations']->count() > 0)
                        @foreach($data['pendingActivations'] as $pending)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-danger text-danger">
                                    <i class="fas fa-pause"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $pending->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted fs-7">
                                    {{ $pending->package->name ?? 'No Package' }} - 
                                    {{ \Carbon\Carbon::parse($pending->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                            <button class="btn btn-sm btn-primary">Aktivasi</button>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fs-2x text-success mb-3"></i>
                            <div class="text-gray-600">Tidak ada pending aktivasi</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Layanan Hari Ini -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day me-2 text-info"></i>Layanan Dijadwalkan Hari Ini
                    </h3>
                </div>
                <div class="card-body">
                    @if($data['todayServices']->count() > 0)
                        @foreach($data['todayServices'] as $service)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-info text-info">
                                    <i class="fas fa-dumbbell"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $service->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted fs-7">
                                    {{ $service->service->name ?? 'No Service' }} - 
                                    {{ \Carbon\Carbon::parse($service->scheduled_date)->format('H:i') }}
                                </div>
                            </div>
                            <span class="badge badge-info">Dijadwalkan</span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fs-2x text-gray-400 mb-3"></i>
                            <div class="text-gray-600">Tidak ada layanan dijadwalkan hari ini</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grafik Check-in Mingguan -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Check-in 7 Hari Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="weeklyCheckinsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Pendapatan Mingguan -->
        <div class="col-12 col-lg-6">
            <div class="card h-lg-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>Pendapatan 7 Hari Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="weeklyRevenueChart" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Check-in Mingguan
const weeklyCheckinsData = @json($data['weeklyCheckins'] ?? []);
const checkinsCtx = document.getElementById('weeklyCheckinsChart');
if (checkinsCtx && weeklyCheckinsData) {
new Chart(checkinsCtx.getContext('2d'), {
    type: 'line',
    data: {
        labels: Object.keys(weeklyCheckinsData),
        datasets: [{
            label: 'Check-in',
            data: Object.values(weeklyCheckinsData),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
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

// Chart Pendapatan Mingguan
const weeklyRevenueData = @json($data['weeklyRevenue'] ?? []);
const revenueCtx = document.getElementById('weeklyRevenueChart');
if (revenueCtx && weeklyRevenueData) {
new Chart(revenueCtx.getContext('2d'), {
    type: 'bar',
    data: {
        labels: Object.keys(weeklyRevenueData),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: Object.values(weeklyRevenueData),
            backgroundColor: '#10b981',
            borderColor: '#059669',
            borderWidth: 1
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
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
}

// Auto refresh setiap 5 menit
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection
