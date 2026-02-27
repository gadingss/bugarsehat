@extends('layouts.app')

@section('title', 'Statistik Membership')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Statistik Membership
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('membership.index') }}" class="text-muted text-hover-primary">Membership</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Statistik</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Statistics Cards -->
            <div class="row g-6 mb-8">
                <div class="col-md-3">
                    <div class="card card-bordered h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-wallet fs-2x text-success mb-3"></i>
                            <div class="fs-2x fw-bold text-success">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</div>
                            <div class="text-gray-600 fs-7">Total Pengeluaran</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-dumbbell fs-2x text-primary mb-3"></i>
                            <div class="fs-2x fw-bold text-primary">{{ $stats['total_visits'] }}</div>
                            <div class="text-gray-600 fs-7">Total Kunjungan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-alt fs-2x text-warning mb-3"></i>
                            <div class="fs-2x fw-bold text-warning">{{ $stats['average_duration'] }}</div>
                            <div class="text-gray-600 fs-7">Rata-rata Hari Aktif</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fs-2x text-info mb-3"></i>
                            <div class="fs-2x fw-bold text-info">{{ $stats['favorite_time'] ?? 'N/A' }}</div>
                            <div class="text-gray-600 fs-7">Waktu Favorit</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-6">
                <!-- Monthly Visits Chart -->
                <div class="col-lg-8">
                    <div class="card card-bordered h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Grafik Kunjungan Bulanan</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyVisitsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Visit Time Distribution -->
                <div class="col-lg-4">
                    <div class="card card-bordered h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Distribusi Waktu Kunjungan</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        <i class="fas fa-sun"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Pagi (06:00 - 12:00)</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: 35%"></div>
                                    </div>
                                </div>
                                <span class="fw-bold text-primary">35%</span>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-warning text-warning">
                                        <i class="fas fa-sun"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Siang (12:00 - 17:00)</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" style="width: 25%"></div>
                                    </div>
                                </div>
                                <span class="fw-bold text-warning">25%</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-info text-info">
                                        <i class="fas fa-moon"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Sore/Malam (17:00 - 22:00)</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info" style="width: 40%"></div>
                                    </div>
                                </div>
                                <span class="fw-bold text-info">40%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership Timeline -->
            <div class="card mt-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Timeline Membership</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if($stats['membership_timeline']->count() > 0)
                        <div class="timeline">
                            @foreach($stats['membership_timeline'] as $index => $membership)
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-{{ $membership['status'] == 'active' ? 'success' : ($membership['status'] == 'expired' ? 'danger' : 'warning') }}">
                                        <i class="fas fa-{{ $membership['status'] == 'active' ? 'check' : ($membership['status'] == 'expired' ? 'times' : 'clock') }} fs-2 text-{{ $membership['status'] == 'active' ? 'success' : ($membership['status'] == 'expired' ? 'danger' : 'warning') }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="pe-3 mb-5">
                                        <div class="fs-5 fw-semibold mb-2">{{ $membership['package_name'] }}</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="text-muted me-2 fs-7">
                                                {{ \Carbon\Carbon::parse($membership['start_date'])->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($membership['end_date'])->format('d M Y') }}
                                            </div>
                                            <span class="badge badge-light-{{ $membership['status'] == 'active' ? 'success' : ($membership['status'] == 'expired' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($membership['status']) }}
                                            </span>
                                        </div>
                                        <div class="text-gray-600 fs-7 mt-1">
                                            Durasi: {{ $membership['duration_days'] }} hari
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="fs-1 fw-bold text-gray-400 mb-3">Belum Ada Data</div>
                            <div class="fs-6 text-gray-600">Belum ada riwayat membership</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Aksi Cepat</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-3">
                            <a href="{{ route('membership.index') }}" class="btn btn-light-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-home fs-2x mb-3"></i>
                                <span class="fw-bold">Dashboard Membership</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('membership.history') }}" class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-history fs-2x mb-3"></i>
                                <span class="fw-bold">Riwayat Lengkap</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('membership.upgrade') }}" class="btn btn-light-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-arrow-up fs-2x mb-3"></i>
                                <span class="fw-bold">Upgrade Membership</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('checkin.index') }}" class="btn btn-light-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-qrcode fs-2x mb-3"></i>
                                <span class="fw-bold">Check-in</span>
                            </a>
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
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Visits Chart
    const monthlyVisitsData = @json($stats['monthly_visits']);
    const monthlyVisitsCtx = document.getElementById('monthlyVisitsChart');
    
    if (monthlyVisitsCtx && monthlyVisitsData) {
        new Chart(monthlyVisitsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: Object.keys(monthlyVisitsData),
                datasets: [{
                    label: 'Kunjungan',
                    data: Object.values(monthlyVisitsData),
                    borderColor: '#3E97FF',
                    backgroundColor: 'rgba(62, 151, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3E97FF',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
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
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    }
});
</script>
@endsection
