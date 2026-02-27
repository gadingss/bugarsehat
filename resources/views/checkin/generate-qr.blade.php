@extends('layouts.app')

@section('title', 'Generate QR Code')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="card card-flush mb-5">
        <div class="card-body text-center py-10">
            <h1 class="fw-bold mb-3">QR Code Check-in</h1>
            <p class="text-gray-600 fs-5">Tunjukkan QR code ini kepada staff untuk check-in.</p>
        </div>
    </div>

    <div class="card card-flush mb-5">
        <div class="card-header">
            <h3 class="card-title">Kode QR Anda</h3>
        </div>
        <div class="card-body text-center py-10">
            <div class="d-flex align-items-center justify-content-center mb-8">
                <div class="symbol symbol-60px symbol-circle me-4">
                    <span class="symbol-label bg-primary text-inverse-primary fs-2">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
                <div class="text-start">
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <div class="text-gray-600">{{ $user->email }}</div>
                </div>
            </div>

            <div class="mb-8">
                <div class="d-inline-block p-4 bg-light rounded" id="qr-code-container">
                    {!! QrCode::size(250)->generate($qrData) !!}
                </div>

                <div class="mt-3">
                    <span class="fw-bold text-primary fs-3" style="letter-spacing: 2px;">
                        {{ $qrData }}
                    </span>
                    <div class="text-muted small">
                        Gunakan kode ini jika pemindaian QR code bermasalah.
                    </div>
                </div>
            </div>

            @if($activeMembership)
            <div class="alert alert-success d-flex align-items-center p-5 mb-8 mw-lg-600px mx-auto">
                <i class="fas fa-check-circle fs-2hx text-success me-4"></i>
                <div class="d-flex flex-column text-start">
                    <h4 class="mb-1 text-success">Membership Aktif</h4>
                    <span class="text-gray-700">
                        {{ $activeMembership->package->name }} -
                        Berlaku hingga {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}
                    </span>
                </div>
            </div>
            @else
            <div class="alert alert-warning d-flex align-items-center p-5 mb-8 mw-lg-600px mx-auto">
                <i class="fas fa-exclamation-triangle fs-2hx text-warning me-4"></i>
                <div class="d-flex flex-column text-start">
                    <h4 class="mb-1 text-warning">Tidak Ada Membership Aktif</h4>
                    <span class="text-gray-700">Anda perlu membership aktif untuk check-in.</span>
                </div>
            </div>
            @endif

            <div class="text-gray-600 fs-6">
                <p class="mb-2">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Kode ini hanya berlaku untuk 1 menit dan 1 kali check-in.
                </p>
                <p>
                    <i class="fas fa-sync text-primary me-2"></i>
                    Halaman akan refresh otomatis untuk membuat kode baru.
                </p>
            </div>
        </div>
    </div>

    <div class="card card-flush">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <a href="{{ route('checkin.index') }}" class="btn btn-light-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Check-in
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <button class="btn btn-primary w-100" onclick="downloadQR()">
                        <i class="fas fa-download me-2"></i>
                        Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh QR code every 60 seconds for security
    const refreshInterval = setInterval(() => {
        location.reload();
    }, 60000); // 60 detik

    // Download QR Code functionality
    function downloadQR() {
        const qrCodeContainer = document.getElementById('qr-code-container');
        // QrCode library generates an SVG, not an IMG. We need to handle this.
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
            ctx.fillStyle = "#FFFFFF"; // Set background to white
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

    // Stop refresh when leaving the page
    window.addEventListener('beforeunload', function(e) {
        clearInterval(refreshInterval);
    });
</script>
@endpush
