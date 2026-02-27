@extends('layouts.app')

@section('title', 'Manfaat Membership')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Manfaat Membership
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
                    <li class="breadcrumb-item text-muted">Manfaat</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if($activeMembership)
                <!-- Current Membership Status -->
                <div class="card mb-8">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Status Membership Anda</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-80px me-5">
                                <span class="symbol-label bg-light-primary text-primary fs-2x fw-bold">
                                    {{ substr($activeMembership->package->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-2">{{ $activeMembership->package->name }} Member</h4>
                                <div class="text-gray-600 mb-3">{{ $activeMembership->package->description }}</div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-success me-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        Aktif hingga {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}
                                    </span>
                                    @if(isset($activeMembership->remaining_visits) && $activeMembership->remaining_visits !== null)
                                    <span class="badge badge-light-info">
                                        <i class="fas fa-ticket-alt me-1"></i>
                                        {{ $activeMembership->remaining_visits == 999 ? 'Unlimited' : $activeMembership->remaining_visits . ' kunjungan tersisa' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Benefits -->
                <div class="row g-6 mb-8">
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3><i class="fas fa-dumbbell text-primary me-2"></i>Akses Fasilitas Gym</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle text-success fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Akses 24/7</div>
                                        <div class="text-gray-600 fs-7">Gym buka 24 jam untuk member {{ $activeMembership->package->name }}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle text-success fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Semua Peralatan</div>
                                        <div class="text-gray-600 fs-7">Akses ke semua peralatan fitness dan cardio</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle text-success fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Area Locker</div>
                                        <div class="text-gray-600 fs-7">Locker gratis untuk menyimpan barang</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">WiFi Gratis</div>
                                        <div class="text-gray-600 fs-7">Koneksi internet berkecepatan tinggi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-bordered h-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3><i class="fas fa-users text-warning me-2"></i>Layanan Personal</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-{{ in_array($activeMembership->package->name, ['Gold', 'Platinum', 'Diamond']) ? 'check-circle text-success' : 'times-circle text-danger' }} fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Personal Trainer</div>
                                        <div class="text-gray-600 fs-7">
                                            @if(in_array($activeMembership->package->name, ['Gold', 'Platinum', 'Diamond']))
                                                Konsultasi gratis dengan personal trainer
                                            @else
                                                Tersedia dengan biaya tambahan
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-{{ in_array($activeMembership->package->name, ['Platinum', 'Diamond']) ? 'check-circle text-success' : 'times-circle text-danger' }} fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Program Diet</div>
                                        <div class="text-gray-600 fs-7">
                                            @if(in_array($activeMembership->package->name, ['Platinum', 'Diamond']))
                                                Konsultasi nutrisi dan program diet
                                            @else
                                                Upgrade ke Platinum untuk akses
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle text-success fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Progress Tracking</div>
                                        <div class="text-gray-600 fs-7">Monitoring kemajuan latihan Anda</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $activeMembership->package->name == 'Diamond' ? 'check-circle text-success' : 'times-circle text-danger' }} fs-2x me-4"></i>
                                    <div>
                                        <div class="fw-bold">Priority Support</div>
                                        <div class="text-gray-600 fs-7">
                                            @if($activeMembership->package->name == 'Diamond')
                                                Dukungan prioritas 24/7
                                            @else
                                                Upgrade ke Diamond untuk akses
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Included Products -->
                @if($membershipProducts->count() > 0)
                <div class="card mb-8">
                    <div class="card-header">
                        <div class="card-title">
                            <h3><i class="fas fa-gift text-success me-2"></i>Produk Termasuk dalam Membership</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            @foreach($membershipProducts as $membershipProduct)
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-bordered">
                                    <div class="card-body text-center">
                                        <div class="symbol symbol-60px mx-auto mb-4">
                                            <span class="symbol-label bg-light-success text-success">
                                                <i class="fas fa-box fs-2x"></i>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold mb-2">{{ $membershipProduct->product->name }}</h5>
                                        <div class="text-gray-600 mb-3">{{ $membershipProduct->product->description }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-gray-600">Kuota:</span>
                                            <span class="fw-bold">{{ $membershipProduct->quota_per_month }} per bulan</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-gray-600">Terpakai:</span>
                                            <span class="fw-bold text-primary">{{ $membershipProduct->used_quota ?? 0 }}</span>
                                        </div>
                                        <div class="progress mt-3">
                                            @php
                                                $percentage = $membershipProduct->quota_per_month > 0 ? 
                                                    (($membershipProduct->used_quota ?? 0) / $membershipProduct->quota_per_month) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-success" style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Available Services -->
                @if($availableServices->count() > 0)
                <div class="card mb-8">
                    <div class="card-header">
                        <div class="card-title">
                            <h3><i class="fas fa-concierge-bell text-info me-2"></i>Layanan Tersedia</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            @foreach($availableServices as $service)
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-bordered">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="symbol symbol-50px me-3">
                                                <span class="symbol-label bg-light-info text-info">
                                                    <i class="fas fa-spa fs-2x"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-1">{{ $service->name }}</h5>
                                                <div class="text-gray-600 fs-7">{{ $service->category }}</div>
                                            </div>
                                        </div>
                                        <div class="text-gray-700 mb-3">{{ $service->description }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fs-2 fw-bold text-{{ $service->price == 0 ? 'success' : 'primary' }}">
                                                {{ $service->price == 0 ? 'GRATIS' : 'Rp ' . number_format($service->price, 0, ',', '.') }}
                                            </span>
                                            <a href="{{ route('services.index') }}" class="btn btn-sm btn-light-primary">
                                                Booking
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Membership Level Comparison -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3><i class="fas fa-layer-group text-primary me-2"></i>Perbandingan Level Membership</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800">
                                        <th>Fitur</th>
                                        <th class="text-center">Trial</th>
                                        <th class="text-center">Silver</th>
                                        <th class="text-center">Gold</th>
                                        <th class="text-center">Platinum</th>
                                        <th class="text-center">Diamond</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Akses Gym</td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Personal Trainer</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Program Diet</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Priority Support</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="bg-light-{{ strtolower($activeMembership->package->name) == 'trial' ? 'info' : (strtolower($activeMembership->package->name) == 'silver' ? 'secondary' : (strtolower($activeMembership->package->name) == 'gold' ? 'warning' : (strtolower($activeMembership->package->name) == 'platinum' ? 'primary' : 'success'))) }}">
                                        <td class="fw-bold">Level Anda Saat Ini</td>
                                        <td class="text-center">{{ $activeMembership->package->name == 'Trial' ? '✓' : '' }}</td>
                                        <td class="text-center">{{ $activeMembership->package->name == 'Silver' ? '✓' : '' }}</td>
                                        <td class="text-center">{{ $activeMembership->package->name == 'Gold' ? '✓' : '' }}</td>
                                        <td class="text-center">{{ $activeMembership->package->name == 'Platinum' ? '✓' : '' }}</td>
                                        <td class="text-center">{{ $activeMembership->package->name == 'Diamond' ? '✓' : '' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        @if($activeMembership->package->name != 'Diamond')
                        <div class="text-center mt-6">
                            <a href="{{ route('membership.upgrade') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-up me-2"></i>
                                Upgrade Membership Sekarang
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

            @else
                <!-- No Active Membership -->
                <div class="card">
                    <div class="card-body text-center py-15">
                        <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak Ada Membership Aktif</div>
                        <div class="fs-6 text-gray-600 mb-8">Anda belum memiliki membership aktif untuk melihat manfaat</div>
                        <a href="{{ route('packet_membership') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            Pilih Paket Membership
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
