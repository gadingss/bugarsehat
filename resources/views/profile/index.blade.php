@extends('layouts.app')
@section('title', 'Profile Saya')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="row g-5 g-xl-6">
        <!-- Profile Card -->
        <div class="col-12 col-lg-4">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Informasi Profile</h3>
                </div>
                <div class="card-body text-center">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="avatar" />
                        @else
                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-3">{{ $user->name }}</h3>
                    <div class="text-gray-600 mb-5">{{ $user->email }}</div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="btn btn-light">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Status -->
        <div class="col-12 col-lg-8">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Status Membership</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('profile.membership-status') }}" class="btn btn-sm btn-light">Detail</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($activeMembership)
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-60px me-5">
                                <span class="symbol-label bg-primary text-inverse-primary">
                                    <span class="fs-1 fw-bold">{{ substr($activeMembership->package->name, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1">{{ $activeMembership->package->name }}</h4>
                                <div class="text-gray-600">{{ $activeMembership->package->description }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-2 fw-bold text-primary">{{ $activeMembership->remaining_visits == 999 ? '∞' : $activeMembership->remaining_visits }}</div>
                                <div class="text-gray-600 fs-7">Sisa Kunjungan</div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light-success rounded p-3">
                                    <div class="fw-semibold text-success">Berlaku Hingga</div>
                                    <div class="text-gray-800">{{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light-info rounded p-3">
                                    <div class="fw-semibold text-info">Sisa Hari</div>
                                    <div class="text-gray-800">{{ $activeMembership->getRemainingDays() }} hari</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak Ada Membership</div>
                            <div class="fs-6 text-gray-600 mb-5">Anda belum memiliki membership aktif</div>
                            <a href="{{ route('packet_membership') }}" class="btn btn-primary">Beli Membership</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-5 g-xl-6 mt-5">
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-primary">{{ $totalVisits }}</div>
                    <div class="text-gray-600 fs-7">Total Kunjungan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-success">{{ $thisMonthVisits }}</div>
                    <div class="text-gray-600 fs-7">Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-warning">{{ $membershipHistory->count() }}</div>
                    <div class="text-gray-600 fs-7">Riwayat Membership</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-info">{{ $recentCheckins->count() }}</div>
                    <div class="text-gray-600 fs-7">Check-in Terakhir</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-5 g-xl-6 mt-5">
        <!-- Recent Check-ins -->
        <div class="col-12 col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Check-in Terakhir</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('profile.visit-history') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentCheckins->count() > 0)
                        @foreach($recentCheckins as $checkin)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-success text-success">
                                    <i class="fas fa-sign-in-alt fs-6"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Check-in</div>
                                <div class="text-muted fs-7">{{ $checkin->checkin_time->format('d M Y H:i') }}</div>
                            </div>
                            <div class="text-end">
                                @if($checkin->checkout_time)
                                    <div class="text-success fs-7">{{ $checkin->getFormattedDuration() }}</div>
                                @else
                                    <span class="badge badge-warning">Aktif</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="text-gray-600">Belum ada check-in</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Membership History -->
        <div class="col-12 col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Membership</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('membership.history') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($membershipHistory->count() > 0)
                        @foreach($membershipHistory as $membership)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary text-primary">
                                    <span class="fw-bold">{{ substr($membership->package->name, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $membership->package->name }}</div>
                                <div class="text-muted fs-7">{{ \Carbon\Carbon::parse($membership->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($membership->end_date)->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-{{ $membership->status == 'active' ? 'success' : ($membership->status == 'expired' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($membership->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="text-gray-600">Belum ada riwayat membership</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
