@extends('layouts.app')
@section('title', 'Kelola Membership')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6" style="background-color: #f9fafb;">
    <!-- Current Membership Status -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            @if($activeMembership)
            <!-- Active Membership Card -->
            <div class="card card-flush shadow-sm border-0 rounded-3 bg-gradient-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-60px me-5">
                                    <span class="symbol-label bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="font-size: 2.5rem; font-weight: 700;">
                                        {{ substr($activeMembership->package->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h2 class="fw-bold text-white mb-1">{{ $activeMembership->package->name }} Membership</h2>
                                    <div class="text-white opacity-75 fs-6">{{ $activeMembership->package->description }}</div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3 text-center shadow-sm">
                                        <div class="fs-2 fw-bold text-white mb-1">{{ $activeMembership->getRemainingDays() }}</div>
                                        <div class="text-white opacity-75 fs-7">Hari Tersisa</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3 text-center shadow-sm">
                                        <div class="fs-2 fw-bold text-white mb-1">{{ $activeMembership->remaining_visits == 999 ? '∞' : $activeMembership->remaining_visits }}</div>
                                        <div class="text-white opacity-75 fs-7">Sisa Kunjungan</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3 text-center shadow-sm">
                                        <div class="fs-2 fw-bold text-white mb-1">{{ $stats['this_month'] }}</div>
                                        <div class="text-white opacity-75 fs-7">Kunjungan Bulan Ini</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3 text-center shadow-sm">
                                        <div class="fs-2 fw-bold text-white mb-1">{{ $stats['total_visits'] }}</div>
                                        <div class="text-white opacity-75 fs-7">Total Kunjungan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-3">
                                <div class="text-white opacity-75 fs-7">Berlaku hingga</div>
                                <div class="fs-3 fw-bold text-white">{{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}</div>
                            </div>
                            @if($activeMembership->getRemainingDays() <= 7)
                            <a href="{{ route('membership.packages') }}" class="btn btn-warning shadow-sm">
                                <i class="fas fa-refresh me-2"></i>Perpanjang Sekarang
                            </a>
                            @else
                            <a href="{{ route('membership.packages') }}" class="btn btn-light shadow-sm">
                                <i class="fas fa-eye me-2"></i>Lihat Paket Lain
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- No Active Membership -->
            <div class="card card-flush shadow-sm border-0 rounded-3">
                <div class="card-body text-center py-10">
                    <div class="symbol symbol-100px symbol-circle mx-auto mb-5">
                        <span class="symbol-label bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fas fa-exclamation-triangle fs-2x"></i>
                        </span>
                    </div>
                    <h2 class="fw-bold mb-3">Tidak Ada Membership Aktif</h2>
                    <p class="text-gray-600 fs-5 mb-5">Anda belum memiliki membership aktif. Pilih paket membership untuk mulai berlatih di gym kami.</p>
                            <a href="{{ route('membership.packages') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-plus me-2"></i>Pilih Paket Membership
                            </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush shadow-sm border-0 rounded-3">
                <div class="card-header">
                    <h3 class="card-title">Aksi Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('membership.packages') }}" class="btn btn-light-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5 shadow-sm rounded-3">
                                <i class="fas fa-shopping-cart fs-2x mb-3"></i>
                                <span class="fw-bold">Beli Paket</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('membership.renewal') }}" class="btn btn-light-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5 shadow-sm rounded-3">
                                <i class="fas fa-refresh fs-2x mb-3"></i>
                                <span class="fw-bold">Perpanjang</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('membership.history') }}" class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5 shadow-sm rounded-3">
                                <i class="fas fa-history fs-2x mb-3"></i>
                                <span class="fw-bold">Riwayat</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('checkin.index') }}" class="btn btn-light-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5 shadow-sm rounded-3">
                                <i class="fas fa-sign-in-alt fs-2x mb-3"></i>
                                <span class="fw-bold">Check-in</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Benefits -->
    @if($activeMembership)
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush shadow-sm border-0 rounded-3">
                <div class="card-header">
                    <h3 class="card-title">Benefit Membership Anda</h3>
                </div>
                <div class="card-body">
                    <div class="row g-5">
                        <!-- Products -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-box text-primary me-2"></i>Produk Tersedia
                            </h5>
                            @if($membershipProducts->count() > 0)
                                @foreach($membershipProducts as $product)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-box fs-6"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $product->product->name }}</div>
                                        <div class="text-gray-600 fs-7">{{ $product->used_quantity }}/{{ $product->quantity }} digunakan</div>
                                    </div>
                                    <div class="text-end">
                                        @if($product->used_quantity < $product->quantity)
                                            <span class="badge badge-success">Tersedia</span>
                                        @else
                                            <span class="badge badge-secondary">Habis</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-gray-600">Tidak ada produk khusus untuk membership ini</div>
                            @endif
                        </div>

                        <!-- Services -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-concierge-bell text-success me-2"></i>Layanan Tersedia
                            </h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-success text-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-dumbbell fs-6"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Akses Gym</div>
                                    <div class="text-gray-600 fs-7">Unlimited akses ke semua peralatan gym</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-info text-info rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-swimming-pool fs-6"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Kolam Renang</div>
                                    <div class="text-gray-600 fs-7">Akses gratis ke kolam renang</div>
                                </div>
                            </div>

                            @if($activeMembership->package->name != 'Trial')
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-spa fs-6"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Sauna</div>
                                    <div class="text-gray-600 fs-7">Akses ke fasilitas sauna</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="row g-5 g-xl-6">
        <div class="col-12">
            <div class="card card-flush shadow-sm border-0 rounded-3">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Terbaru</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('checkin.history') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        @foreach($recentActivities as $activity)
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-sign-in-alt fs-6"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Check-in Gym</div>
                                <div class="text-gray-600 fs-7">
                                    {{ $activity->checkin_time->format('d M Y, H:i') }}
                                    @if($activity->checkout_time)
                                        - {{ $activity->checkout_time->format('H:i') }}
                                        ({{ $activity->getFormattedDuration() }})
                                    @else
                                        <span class="badge badge-warning ms-2">Aktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="text-gray-600">Belum ada aktivitas</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Auto refresh membership status every 5 minutes
setInterval(function() {
    // Only refresh if membership is about to expire
    @if($activeMembership && $activeMembership->getRemainingDays() <= 1)
    location.reload();
    @endif
}, 300000); // 5 minutes
</script>
@endsection
