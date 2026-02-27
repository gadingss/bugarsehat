@extends('layouts.app')
@section('title', 'Check-in')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <!-- Header -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-body text-center py-10">
                    <h1 class="fw-bold mb-3">Check-in Gym</h1>
                    <p class="text-gray-600 fs-5">Selamat datang di Bugar Sehat Gym & Yoga</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Status & Check-in -->
    <div class="row g-5 g-xl-6 mb-5">
        <!-- Membership Status -->
        <div class="col-12 col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Status Membership</h3>
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
                                <div class="text-gray-600">Berlaku hingga {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-2 fw-bold text-primary">{{ $activeMembership->remaining_visits == 999 ? '∞' : $activeMembership->remaining_visits }}</div>
                                <div class="text-gray-600 fs-7">Sisa Kunjungan</div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light-success rounded p-3 text-center">
                                    <div class="fs-2x fw-bold text-success">{{ $activeMembership->getRemainingDays() }}</div>
                                    <div class="text-gray-600 fs-7">Hari Tersisa</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light-info rounded p-3 text-center">
                                    <div class="fs-2x fw-bold text-info">{{ $stats['this_month'] }}</div>
                                    <div class="text-gray-600 fs-7">Kunjungan Bulan Ini</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak Ada Membership</div>
                            <div class="fs-6 text-gray-600 mb-5">Anda perlu membership aktif untuk check-in</div>
                            <a href="{{ route('packet_membership') }}" class="btn btn-primary">Beli Membership</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Check-in Actions -->
        <div class="col-12 col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <h3 class="card-title">Check-in Hari Ini</h3>
                </div>
                <div class="card-body">
                    @if($activeMembership)
                        @if(!$todayCheckin)
                            <!-- Belum Check-in -->
                            <div class="text-center py-5">
                                <div class="symbol symbol-100px symbol-circle mx-auto mb-5">
                                    <span class="symbol-label bg-light-success text-success">
                                        <i class="fas fa-sign-in-alt fs-2x"></i>
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-3">Siap untuk Check-in?</h4>
                                <p class="text-gray-600 mb-5">Klik tombol di bawah untuk memulai sesi latihan Anda</p>
                                <button class="btn btn-success btn-lg" onclick="performCheckin()">
                                    <i class="fas fa-sign-in-alt me-2"></i>Check-in Sekarang
                                </button>
                            </div>
                        @elseif(!$todayCheckin->checkout_time)
                            <!-- Sudah Check-in, belum Check-out -->
                            <div class="text-center py-5">
                                <div class="symbol symbol-100px symbol-circle mx-auto mb-5">
                                    <span class="symbol-label bg-light-warning text-warning">
                                        <i class="fas fa-clock fs-2x"></i>
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-3">Sesi Aktif</h4>
                                <p class="text-gray-600 mb-3">Check-in: {{ $todayCheckin->checkin_time->format('H:i') }}</p>
                                <p class="text-gray-600 mb-5">Durasi: <span id="session-duration">{{ $todayCheckin->checkin_time->diffForHumans() }}</span></p>
                                <button class="btn btn-warning btn-lg" onclick="performCheckout()">
                                    <i class="fas fa-sign-out-alt me-2"></i>Check-out
                                </button>
                            </div>
                        @else
                            <!-- Sudah Check-out -->
                            <div class="text-center py-5">
                                <div class="symbol symbol-100px symbol-circle mx-auto mb-5">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        <i class="fas fa-check fs-2x"></i>
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-3">Sesi Selesai</h4>
                                <p class="text-gray-600 mb-3">Check-in: {{ $todayCheckin->checkin_time->format('H:i') }}</p>
                                <p class="text-gray-600 mb-3">Check-out: {{ $todayCheckin->checkout_time->format('H:i') }}</p>
                                <p class="text-gray-600 mb-5">Durasi: {{ $todayCheckin->getFormattedDuration() }}</p>
                                <div class="text-success fw-bold">Terima kasih telah berlatih hari ini!</div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <div class="text-gray-600">Membership diperlukan untuk check-in</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Aksi Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            @if(Auth::user()->hasRole('User:Owner'))
                                <a href="{{ route('checkin.owner-scanner') }}" class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fas fa-qrcode fs-2x mb-3"></i>
                                    <span class="fw-bold">Scan QR Owner</span>
                                </a>
                            @elseif(Auth::user()->hasRole('User:Staff'))
                                <a href="{{ route('checkin.staff-scanner') }}" class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fas fa-qrcode fs-2x mb-3"></i>
                                    <span class="fw-bold">Scan QR Staff</span>
                                </a>
                            @else
                                <a href="{{ route('checkin.qr-scan') }}" class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fas fa-qrcode fs-2x mb-3"></i>
                                    <span class="fw-bold">Scan QR</span>
                                </a>
                            @endif
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="btn btn-light-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5" data-bs-toggle="modal" data-bs-target="#modalQrCode">
                                <i class="fas fa-qrcode fs-2x mb-3"></i>
                                <span class="fw-bold">QR Saya</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('checkin.history') }}" class="btn btn-light-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-history fs-2x mb-3"></i>
                                <span class="fw-bold">Riwayat</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('services.index') }}" class="btn btn-light-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                <i class="fas fa-concierge-bell fs-2x mb-3"></i>
                                <span class="fw-bold">Layanan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-5 g-xl-6">
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-primary">{{ $stats['today'] }}</div>
                    <div class="text-gray-600 fs-7">Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-success">{{ $stats['this_week'] }}</div>
                    <div class="text-gray-600 fs-7">Minggu Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-warning">{{ $stats['this_month'] }}</div>
                    <div class="text-gray-600 fs-7">Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-body text-center">
                    <div class="fs-2x fw-bold text-info">{{ $stats['total_visits'] }}</div>
                    <div class="text-gray-600 fs-7">Total</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="modalQrCode" tabindex="-1" aria-labelledby="modalQrCodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQrCodeLabel">QR Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                {!! QrCode::size(200)->generate($qrData ?? route('checkin.qr-scan', ['user' => Auth::id()])) !!}
                <p class="mt-3">Scan QR code ini untuk check-in</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function performCheckin() {
    Swal.fire({
        title: 'Konfirmasi Check-in',
        text: "Apakah Anda ingin melakukan check-in sekarang?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Check-in",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang melakukan check-in',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            fetch('{{ route("checkin.checkin") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    icon: 'error'
                });
            });
        }
    });
}

function performCheckout() {
    Swal.fire({
        title: 'Konfirmasi Check-out',
        text: "Apakah Anda ingin melakukan check-out sekarang?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Check-out",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang melakukan check-out',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            fetch('{{ route("checkin.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    icon: 'error'
                });
            });
        }
    });
}

// Update session duration every minute
@if($todayCheckin && !$todayCheckin->checkout_time)
setInterval(function() {
    const checkinTime = new Date('{{ $todayCheckin->checkin_time->toISOString() }}');
    const now = new Date();
    const diff = now - checkinTime;
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    let duration = '';
    if (hours > 0) {
        duration += hours + ' jam ';
    }
    duration += minutes + ' menit';
    
    document.getElementById('session-duration').textContent = duration;
}, 60000);
@endif
</script>
@endsection
