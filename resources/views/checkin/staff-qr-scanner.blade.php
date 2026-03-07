@extends('layouts.app')

@section('title', 'Staff QR Scanner')

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
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

        {{-- BAGIAN INI TELAH DIUBAH SESUAI PERMINTAAN --}}
        <div class="row g-5 g-xl-6 mb-5">

            {{-- 1. KOTAK STATUS MEMBERSHIP DIHAPUS --}}

            {{-- 2. KOTAK CHECK-IN DIUBAH MENJADI LEBAR PENUH DAN SELALU MENAMPILKAN TOMBOL --}}
            <div class="col-12">
                <div class="card card-flush h-100">
                    <div class="card-header">
                        <h3 class="card-title">Scanner QR Code Staff</h3>
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
                                        <input type="text" id="manual-member-id" class="form-control"
                                            placeholder="Masukkan Kode Manual QR (contoh: EYDB2JJW)">
                                        <button class="btn btn-primary" onclick="manualCheckin()">
                                            <i class="fas fa-keyboard me-2"></i>Check-in Manual
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                    <a href="{{ route('checkin.owner-scanner') }}"
                                        class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fas fa-qrcode fs-2x mb-3"></i>
                                        <span class="fw-bold">Scan QR Owner</span>
                                    </a>
                                @elseif(Auth::user()->hasRole('User:Staff'))
                                    <a href="{{ route('checkin.staff-scanner') }}"
                                        class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fas fa-qrcode fs-2x mb-3"></i>
                                        <span class="fw-bold">Scan QR Staff</span>
                                    </a>
                                @else
                                    <a href="{{ route('checkin.qr-scan') }}"
                                        class="btn btn-light-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fas fa-qrcode fs-2x mb-3"></i>
                                        <span class="fw-bold">Scan QR</span>
                                    </a>
                                @endif
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="#"
                                    class="btn btn-light-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5"
                                    data-bs-toggle="modal" data-bs-target="#modalQrCode">
                                    <i class="fas fa-qrcode fs-2x mb-3"></i>
                                    <span class="fw-bold">QR Saya</span>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="{{ route('checkin.history') }}"
                                    class="btn btn-light-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fas fa-history fs-2x mb-3"></i>
                                    <span class="fw-bold">Riwayat</span>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="{{ route('services.index') }}"
                                    class="btn btn-light-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                                    <i class="fas fa-concierge-bell fs-2x mb-3"></i>
                                    <span class="fw-bold">Layanan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-6">
            <div class="col-6 col-lg-3">
                <div class="card card-flush">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold text-primary">{{ $stats['today'] ?? 0 }}</div>
                        <div class="text-gray-600 fs-7">Hari Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card card-flush">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold text-success">{{ $stats['this_week'] ?? 0 }}</div>
                        <div class="text-gray-600 fs-7">Minggu Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card card-flush">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold text-warning">{{ $stats['this_month'] ?? 0 }}</div>
                        <div class="text-gray-600 fs-7">Bulan Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card card-flush">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold text-info">{{ $stats['total_visits'] ?? 0 }}</div>
                        <div class="text-gray-600 fs-7">Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrcodeScanner = null;

        function onScanSuccess(decodedText, decodedResult) {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop();
            }
            processQrCode(decodedText);
        }

        function onScanFailure(error) {
            console.warn(`QR scan error = ${error}`);
        }

        function startQrScanner() {
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
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses QR code',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                        setTimeout(startQrScanner, 2000);
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error'
                        }).then(() => {
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
                        setTimeout(startQrScanner, 1000);
                    });
                });
        }

        function manualCheckin() {
            const rawInput = document.getElementById('manual-member-id').value.trim();

            if (!rawInput) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Masukkan kode manual terlebih dahulu',
                    icon: 'error'
                });
                return;
            }

            if (rawInput.length !== 8) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Format kode manual harus 8 karakter',
                    icon: 'error'
                });
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses check-in manual',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("checkin.manual-staff") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ manual_code: rawInput })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    showCheckinSuccess(data.data);
                    document.getElementById('manual-member-id').value = '';
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

        document.addEventListener('DOMContentLoaded', function () {
            startQrScanner();
        });

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                setTimeout(startQrScanner, 500);
            } else if (html5QrcodeScanner) {
                html5QrcodeScanner.stop();
            }
        });

        document.getElementById('manual-member-id').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                manualCheckin();
            }
        });
    </script>
@endsection