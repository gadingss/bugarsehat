@extends('layouts.app')

@section('title', 'Generate QR Code')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">

    {{-- QR Card --}}
    <div class="card card-flush mb-5">
        <div class="card-body text-center py-10">
            <h1 class="fw-bold mb-2">QR Code Check-in Saya</h1>
            <p class="text-gray-500 fs-6">Tunjukkan QR code ini kepada staff untuk check-in layanan atau gym.</p>

            <div class="d-flex align-items-center justify-content-center mb-6 mt-4">
                <div class="symbol symbol-55px symbol-circle me-4">
                    <span class="symbol-label bg-primary text-inverse-primary fs-2 fw-bold">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
                <div class="text-start">
                    <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                    <div class="text-gray-500 fs-7">{{ $user->email }}</div>
                </div>
            </div>

            <div class="d-inline-block p-4 bg-white border border-2 border-gray-200 rounded shadow-sm mb-5" id="qr-code-container">
                {!! QrCode::size(220)->generate($qrData) !!}
            </div>

            <div class="mt-2 mb-6">
                <span class="fw-bold text-primary fs-4 font-monospace" style="letter-spacing: 3px;">{{ $manualCode }}</span>
                <div class="text-muted fs-8 mt-1">Gunakan kode ini jika QR tidak bisa dipindai</div>
            </div>

            @if($activeMembership)
            <div class="alert alert-success d-flex align-items-center p-4 mb-0 mw-lg-500px mx-auto text-start">
                <i class="fas fa-check-circle fs-2 text-success me-3"></i>
                <div>
                    <div class="fw-bold text-success">Membership Aktif: {{ $activeMembership->package->name }}</div>
                    <div class="text-gray-600 fs-7">
                        Berlaku s/d {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}
                        @if($activeMembership->remaining_visits != 999)
                            · Sisa kunjungan: <strong>{{ $activeMembership->remaining_visits }}x</strong>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning d-flex align-items-center p-4 mb-0 mw-lg-500px mx-auto text-start">
                <i class="fas fa-exclamation-triangle fs-2 text-warning me-3"></i>
                <div>
                    <div class="fw-bold text-warning">Tidak Ada Membership Aktif</div>
                    <div class="text-gray-600 fs-7">Anda masih bisa check-in jika ada kelas yang terdaftar hari ini.</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Jadwal Kelas Terdaftar --}}
    <div class="card card-flush mb-5">
        <div class="card-header pt-5">
            <h3 class="card-title">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                Jadwal Kelas Terdaftar
            </h3>
        </div>
        <div class="card-body py-3">
            @forelse($upcomingBookings as $booking)
            @php
                $schedule = $booking->schedule;
                $isToday = \Carbon\Carbon::parse($schedule->start_time)->isToday();
                $now = now();
                $canCheckin = $now >= \Carbon\Carbon::parse($schedule->start_time)->subMinutes(30)
                           && $now <= \Carbon\Carbon::parse($schedule->end_time);
            @endphp
            <div class="d-flex align-items-center border border-{{ $isToday ? ($canCheckin ? 'success' : 'warning') : 'gray-200' }} rounded p-4 mb-3">
                <div class="symbol symbol-45px me-4">
                    <span class="symbol-label bg-light-{{ $isToday ? ($canCheckin ? 'success' : 'warning') : 'primary' }}">
                        <i class="fas fa-dumbbell text-{{ $isToday ? ($canCheckin ? 'success' : 'warning') : 'primary' }}"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark fs-6">{{ $schedule->title }}</div>
                    <div class="text-muted fs-7">
                        @if($schedule->service)
                            <span class="badge badge-light-primary me-2">{{ $schedule->service->name }}</span>
                        @endif
                        {{ $schedule->trainer->name ?? 'Trainer' }}
                    </div>
                    <div class="text-gray-600 fs-7 mt-1">
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('D, d M Y') }}
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                    </div>
                </div>
                <div class="text-end">
                    @if($canCheckin)
                        <span class="badge badge-light-success px-3 py-2 fw-bold">
                            <i class="fas fa-door-open me-1"></i>Check-in Dibuka
                        </span>
                    @elseif($isToday)
                        <span class="badge badge-light-warning px-3 py-2 fw-bold">
                            <i class="fas fa-hourglass-half me-1"></i>Belum Waktunya
                        </span>
                    @else
                        <span class="badge badge-light-primary px-3 py-2 fw-bold">
                            <i class="fas fa-calendar me-1"></i>Mendatang
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-8">
                <i class="fas fa-calendar-times fs-3x text-gray-200 mb-3 d-block"></i>
                Tidak ada jadwal kelas terdaftar.
                <br>
                <a href="{{ route('services.my-bookings') }}" class="btn btn-sm btn-light-primary mt-3">
                    Lihat & Gunakan Kuota
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Sisa Kuota Bundling --}}
    @if($quotaServices->count() > 0)
    <div class="card card-flush mb-5">
        <div class="card-header pt-5">
            <h3 class="card-title">
                <i class="fas fa-ticket-alt text-success me-2"></i>
                Kuota Layanan Tersisa (Dari Paket)
            </h3>
        </div>
        <div class="card-body py-3">
            @foreach($quotaServices as $trans)
            <div class="d-flex align-items-center justify-content-between border border-gray-200 rounded p-3 mb-2">
                <div class="d-flex align-items-center">
                    <span class="symbol symbol-35px me-3">
                        <span class="symbol-label bg-light-success">
                            <i class="fas fa-layer-group text-success"></i>
                        </span>
                    </span>
                    <div>
                        <div class="fw-bold fs-7">{{ $trans->service->name ?? 'Layanan' }}</div>
                        <div class="text-muted fs-8">Dari paket membership</div>
                    </div>
                </div>
                <span class="badge badge-light-success fs-7 px-3 py-2">
                    {{ $trans->serviceSessions->count() }} sesi tersisa
                </span>
            </div>
            @endforeach
            <div class="mt-3 text-center">
                <a href="{{ route('services.my-bookings') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-calendar-plus me-1"></i> Gunakan Kuota Sekarang
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="card card-flush">
        <div class="card-body py-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <a href="{{ route('checkin.index') }}" class="btn btn-light-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Check-in
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <button class="btn btn-primary w-100" onclick="downloadQR()">
                        <i class="fas fa-download me-2"></i>Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function downloadQR() {
        const qrCodeContainer = document.getElementById('qr-code-container');
        const svgElement = qrCodeContainer.querySelector('svg');
        if (!svgElement) return;

        const serializer = new XMLSerializer();
        const svgString = serializer.serializeToString(svgElement);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            const pngUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = pngUrl;
            link.download = 'qr-checkin-{{ $user->name }}_{{ date("Ymd-Hi") }}.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        img.src = 'data:image/svg+xml;base64,' + window.btoa(svgString);
    }
</script>
@endpush
