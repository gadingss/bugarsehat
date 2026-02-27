@extends('layouts.app')

@section('title', 'Upgrade Membership')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Upgrade Membership
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
                    <li class="breadcrumb-item text-muted">Upgrade</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Berhasil!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Error!</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Current Membership Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Membership Saat Ini</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-primary text-primary fs-1 fw-bold">
                                {{ substr($activeMembership->package->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1">{{ $activeMembership->package->name }}</h4>
                            <div class="text-gray-600 mb-2">{{ $activeMembership->package->description }}</div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light-success me-3">
                                    <i class="fas fa-calendar me-1"></i>
                                    Sisa {{ $activeMembership->getRemainingDays() }} hari
                                </span>
                                @if(isset($activeMembership->remaining_visits) && $activeMembership->remaining_visits !== null)
                                <span class="badge badge-light-info">
                                    <i class="fas fa-ticket-alt me-1"></i>
                                    {{ $activeMembership->remaining_visits == 999 ? 'Unlimited' : $activeMembership->remaining_visits . ' kunjungan' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fs-2 fw-bold text-primary">Rp {{ number_format($activeMembership->package->price, 0, ',', '.') }}</div>
                            <div class="text-gray-600 fs-7">per {{ $activeMembership->package->duration_days }} hari</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upgrade Options -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Pilihan Upgrade</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if($upgradePackages->count() > 0)
                        <div class="row g-6">
                            @foreach($upgradePackages as $package)
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-bordered h-100">
                                    <div class="card-header text-center pt-8">
                                        <div class="symbol symbol-80px mb-5">
                                            <span class="symbol-label bg-light-{{ $package->name == 'Silver' ? 'secondary' : ($package->name == 'Gold' ? 'warning' : ($package->name == 'Platinum' ? 'primary' : 'success')) }} text-{{ $package->name == 'Silver' ? 'secondary' : ($package->name == 'Gold' ? 'warning' : ($package->name == 'Platinum' ? 'primary' : 'success')) }} fs-2x fw-bold">
                                                {{ substr($package->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <h3 class="fw-bold mb-2">{{ $package->name }}</h3>
                                        <div class="fs-2 fw-bold text-primary mb-1">
                                            Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </div>
                                        <div class="text-gray-600 fs-7">per {{ $package->duration_days }} hari</div>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="text-gray-700 mb-5">{{ $package->description }}</div>
                                        
                                        <div class="mb-5">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-gray-600">Durasi:</span>
                                                <span class="fw-bold">{{ $package->duration_days }} hari</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-gray-600">Kunjungan:</span>
                                                <span class="fw-bold">{{ $package->max_visits == 999 ? 'Unlimited' : $package->max_visits . ' kali' }}</span>
                                            </div>
                                            @php
                                                $remainingDays = $activeMembership->getRemainingDays();
                                                $dailyRate = $package->price / $package->duration_days;
                                                $upgradeCost = $dailyRate * $remainingDays;
                                            @endphp
                                            <div class="separator my-3"></div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-gray-600">Biaya Upgrade:</span>
                                                <span class="fw-bold text-success">Rp {{ number_format($upgradeCost, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        
                                        <form action="{{ route('membership.process-upgrade') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                                            <button type="submit" class="btn btn-{{ $package->name == 'Silver' ? 'secondary' : ($package->name == 'Gold' ? 'warning' : ($package->name == 'Platinum' ? 'primary' : 'success')) }} w-100">
                                                <i class="fas fa-arrow-up me-2"></i>
                                                Upgrade ke {{ $package->name }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak Ada Upgrade</div>
                            <div class="fs-6 text-gray-600 mb-5">Anda sudah menggunakan paket membership tertinggi</div>
                            <a href="{{ route('membership.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Kembali ke Membership
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upgrade Benefits Info -->
            @if($upgradePackages->count() > 0)
            <div class="card mt-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Keuntungan Upgrade</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock fs-2x text-primary me-4"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">Perpanjangan Otomatis</h5>
                                    <div class="text-gray-600 fs-7">Membership diperpanjang sesuai paket baru</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-star fs-2x text-warning me-4"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">Fitur Premium</h5>
                                    <div class="text-gray-600 fs-7">Akses ke fasilitas dan layanan premium</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calculator fs-2x text-success me-4"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">Biaya Proporsional</h5>
                                    <div class="text-gray-600 fs-7">Hanya bayar sesuai sisa waktu membership</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation for upgrade
    const upgradeForms = document.querySelectorAll('form[action*="process-upgrade"]');
    upgradeForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const packageName = form.querySelector('input[name="package_id"]').closest('.card').querySelector('h3').textContent;
            
            Swal.fire({
                title: 'Konfirmasi Upgrade',
                text: `Apakah Anda yakin ingin upgrade ke paket ${packageName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Upgrade!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
