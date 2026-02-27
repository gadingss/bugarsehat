@extends('layouts.app')
@section('title', 'Owner QR Scanner')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <!-- Header -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-body text-center py-10">
                    <h1 class="fw-bold mb-3">QR Scanner untuk Owner</h1>
                    <p class="text-gray-600 fs-5">Scan QR code member untuk check-in otomatis dengan akses penuh</p>
                    <div class="d-flex justify-content-center gap-4 mt-4">
                        <div class="text-center">
                            <div class="badge badge-light-primary fs-6 px-4 py-2">
                                <i class="fas fa-users me-2"></i>
                                <span id="total-checkins-today">0</span> Total Check-in Hari Ini
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="badge badge-light-success fs-6 px-4 py-2">
                                <i class="fas fa-user-check me-2"></i>
                                <span id="active-checkins">0</span> Sedang Aktif
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="badge badge-light-info fs-6 px-4 py-2">
                                <i class="fas fa-user-tie me-2"></i>
                                <span id="staff-checkins">0</span> Oleh Staff
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Section -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Scanner QR Code</h3>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        <div id="qr-reader-results" class="mt-5"></div>
                    </div>
                    
                    <!-- Manual Input Fallback -->
                    <div class="mt-5">
                        <h5 class="text-center mb-4">Atau masukkan ID Member secara manual</h5>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="text" id="manual-member-id" class="form-control" placeholder="Masukkan ID Member">
                                    <button class="btn btn-primary" onclick="manualCheckin()">
                                        <i class="fas fa-user-check me-2"></i>Check-in Manual
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Filter Check-in</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-select" id="filter-type">
                                <option value="all">Semua Check-in</option>
                                <option value="staff">Check-in oleh Staff</option>
                                <option value="self">Check-in Mandiri</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filter-status">
                                <option value="all">Semua Status</option>
                                <option value="active">Sedang Aktif</option>
                                <option value="completed">Sudah Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                            <button class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="row g-5 g-xl-6">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Check-in Terakhir (Hari Ini)</h3>
                    <div class="card-toolbar">
                        <button class="btn btn-sm btn-light-primary" onclick="refreshRecentCheckins()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama Member</th>
                                    <th>Paket</th>
                                    <th>Check-in oleh</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recent-checkins">
                                @foreach(\App\Models\CheckinLog::today()->with(['user', 'membership.package', 'staff'])->orderBy('checkin_time', 'desc')->limit(10)->get() as $log)
                                <tr data-type="{{ $log->staff ? 'staff' : 'self' }}" data-status="{{ $log->checkout_time ? 'completed' : 'active' }}">
                                    <td>{{ $log->checkin_time->format('H:i') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px symbol-circle me-3">
                                                <span class="symbol-label bg-light-primary text-primary">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $log->user->name }}</div>
                                                <div class="text-muted fs-7">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">
                                            {{ $log->membership->package->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->staff)
                                            <span class="badge badge-light-success">
                                                <i class="fas fa-user-tie me-1"></i>{{ $log->staff->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-light-info">
                                                <i class="fas fa-user me-1"></i>Mandiri
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->checkout_time)
                                            <span class="badge badge-light-success">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        @else
                                            <span class="badge badge-light-warning">
                                                <i class="fas fa-clock me-1"></i>Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$log->checkout_time)
                                        <button class="btn btn-sm btn-warning" onclick="forceCheckout({{ $log->id }})">
                                            <i class="fas fa-sign-out-alt"></i> Force Checkout
                                        </button>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Result Modal -->
<div class="modal fade" id="checkinResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Check-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="checkin-result-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrcodeScanner = null;

function onScanSuccess(decodedText, decodedResult) {
    // Stop scanning
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop();
    }
    
    // Process QR code
    processQrCode(decodedText);
}

function onScanFailure(error) {
    // Handle scan failure, usually better to ignore and keep scanning
    console.warn(`QR scan error = ${error}`);
}

function startQrScanner() {
    // Clear previous scanner if exists
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
    
    html5QrcodeScanner = new Html5Qrcode("qr-reader");
    
    const config = { 
        fps: 10, 
        qrbox: { width: 250, height: 250 } 
    };

    html5QrcodeScanner.start(
        { facingMode: "environment" }, 
        config, 
        onScanSuccess, 
        onScanFailure
    ).catch(err => {
        console.error("Unable to start scanning", err);
        Swal.fire({
            title: 'Error!',
            text: 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.',
            icon: 'error'
        });
    });
}

function processQrCode(qrData) {
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memproses QR code',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send AJAX request
    fetch('{{ route("checkin.staff-scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            qr_code: qrData
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            showCheckinSuccess(data.data);
            refreshRecentCheckins();
            updateStatistics();
            // Restart scanner after successful checkin
            setTimeout(startQrScanner, 2000);
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error'
            }).then(() => {
                // Restart scanner after error
                setTimeout(startQrScanner, 1000);
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan sistem',
            icon: 'error'
        }).then(() => {
            // Restart scanner after error
            setTimeout(startQrScanner, 1000);
        });
    });
}

function manualCheckin() {
    const memberId = document.getElementById('manual-member-id').value.trim();
    
    if (!memberId) {
        Swal.fire({
            title: 'Error!',
            text: 'Masukkan ID member terlebih dahulu',
            icon: 'error'
        });
        return;
    }

    // Validate member ID is numeric
    if (!/^\d+$/.test(memberId)) {
        Swal.fire({
            title: 'Error!',
            text: 'ID member harus berupa angka',
            icon: 'error'
        });
        return;
    }

    // Generate QR code format with actual member ID
    const qrData = `BUGAR_SEHAT_${memberId}_${Date.now()}`;
    processQrCode(qrData);
}

function showCheckinSuccess(data) {
    const content = `
        <div class="text-center">
            <div class="symbol symbol-100px symbol-circle mx-auto mb-4">
                <span class="symbol-label bg-light-success text-success">
                    <i class="fas fa-check fs-2x"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-3">Check-in Berhasil!</h4>
            <p class="text-gray-600 mb-3">Member: <strong>${data.member_name}</strong></p>
            <p class="text-gray-600 mb-3">Waktu: ${data.checkin_time}</p>
            <p class="text-gray-600 mb-3">Sisa kunjungan: ${data.remaining_visits}</p>
            <p class="text-gray-600">Check-in oleh: ${data.checked_in_by}</p>
        </div>
    `;
    
    document.getElementById('checkin-result-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('checkinResultModal')).show();
}

function updateStatistics() {
    fetch('{{ route("checkin.owner-recent-checkins") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const checkins = data.checkins || [];
                const total = checkins.length;
                const active = checkins.filter(c => !c.checkout_time).length;
                const staff = checkins.filter(c => c.staff_id).length;
                
                document.getElementById('total-checkins-today').textContent = total;
                document.getElementById('active-checkins').textContent = active;
                document.getElementById('staff-checkins').textContent = staff;
            }
        })
        .catch(error => {
            console.error('Error updating statistics:', error);
        });
}

function refreshRecentCheckins() {
    // Refresh the recent checkins table
    fetch('{{ route("checkin.owner-recent-checkins") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('recent-checkins').innerHTML = data.html;
                updateStatistics();
            }
        })
        .catch(error => {
            console.error('Error refreshing checkins:', error);
        });
}

function applyFilters() {
    const typeFilter = document.getElementById('filter-type').value;
    const statusFilter = document.getElementById('filter-status').value;
    
    const rows = document.querySelectorAll('#recent-checkins tr');
    
    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const status = row.getAttribute('data-status');
        
        let show = true;
        
        if (typeFilter !== 'all' && type !== typeFilter) {
            show = false;
        }
        
        if (statusFilter !== 'all' && status !== statusFilter) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filter-type').value = 'all';
    document.getElementById('filter-status').value = 'all';
    
    const rows = document.querySelectorAll('#recent-checkins tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function forceCheckout(checkinId) {
    Swal.fire({
        title: 'Konfirmasi Force Checkout',
        text: 'Apakah Anda yakin ingin melakukan force checkout untuk member ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Force Checkout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang melakukan force checkout',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/checkin/${checkinId}/force-checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success'
                    });
                    refreshRecentCheckins();
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.close();
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

// Start QR scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    startQrScanner();
    updateStatistics();
});

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Restart scanner when page becomes visible
        setTimeout(startQrScanner, 500);
        updateStatistics();
    } else if (html5QrcodeScanner) {
        html5QrcodeScanner.stop();
    }
});

// Handle manual input with Enter key
document.getElementById('manual-member-id').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        manualCheckin();
    }
});
</script>
@endsection
