@extends('layouts.app')

@section('title', 'Pembayaran Layanan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-flush">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Pembayaran Layanan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="fw-bold mb-3">Detail Layanan</h5>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-60px symbol-circle me-4">
                                        <div class="symbol-label bg-light-info">
                                            <i class="fas fa-dumbbell text-info fs-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $transaction->service->name }}</h6>
                                        <span class="text-muted">{{ $transaction->service->category }}</span>
                                    </div>
                                </div>

                                @if($transaction->trainer)
                                    <div class="d-flex align-items-center mb-4 p-3 bg-light-primary rounded">
                                        <div class="symbol symbol-40px me-3">
                                            <span class="symbol-label bg-primary text-white">
                                                <i class="fas fa-user-tie"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">Personal Trainer</div>
                                            <div class="fw-bold">{{ $transaction->trainer->name }}</div>
                                        </div>
                                    </div>
                                @endif

                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">Jadwal</td>
                                        <td class="fw-bold">{{ $transaction->scheduled_date->format('d M Y, H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Total Bayar</td>
                                        <td class="fw-bold text-primary fs-5">Rp
                                            {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>

                                @if($transaction->notes)
                                    <div class="mt-3">
                                        <div class="text-muted fs-7">Catatan:</div>
                                        <div class="fs-7 italic">{{ $transaction->notes }}</div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Konfirmasi Pembayaran</h5>

                                @if(isset($snapToken) && $snapToken)
                                    <div class="mb-8">
                                        <div class="alert alert-primary d-flex align-items-center p-5 mb-5">
                                            <i class="fas fa-credit-card fs-2hx text-primary me-4"></i>
                                            <div class="d-flex flex-column">
                                                <h4 class="fw-bold text-primary">Pembayaran Online (Midtrans)</h4>
                                                <span>Bayar instan via QRIS, Bank Transfer, atau Virtual Account.</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success w-100 py-4 fs-4 fw-bold shadow-sm mb-4"
                                            id="pay-button">
                                            <i class="fas fa-wallet me-2"></i>Bayar Sekarang dengan Midtrans
                                        </button>
                                        <div class="separator separator-content border-dark my-10">
                                            <span class="w-250px fw-bold text-muted">ATAU BAYAR MANUAL</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="alert alert-info py-3 px-4 mb-5">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-university me-2"></i>
                                        <h6 class="fw-bold mb-0">Transfer Bank Manual</h6>
                                    </div>
                                    <div class="p-2 rounded bg-white bg-opacity-50">
                                        <div class="fs-7">Bank: <strong>BCA</strong></div>
                                        <div class="fs-7">No. Rekening: <strong>1234567890</strong></div>
                                        <div class="fs-7">Atas Nama: <strong>Bugar Sehat Gym</strong></div>
                                    </div>
                                </div>

                                <form action="{{ route('services.confirm-payment', $transaction->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Upload Bukti Transfer Manual</label>
                                        <input type="file" class="form-control" name="payment_proof" accept="image/*,.pdf"
                                            required>
                                        <div class="form-text fs-8 text-muted mt-1">Format: JPG, PNG, PDF (Max 2MB)</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                        <i class="fas fa-check-circle me-2"></i>Kirim Bukti Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-4">
                        <div class="text-center">
                            <p class="text-gray-600 fs-7 mb-0">
                                <i class="fas fa-shield-alt me-1"></i> Transaksi Anda aman. Staff kami akan memverifikasi
                                pembayaran Anda dalam waktu maksimal 24 jam.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
        @if(isset($snapToken) && $snapToken)
            <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
            <script type="text/javascript">
                const payButton = document.getElementById('pay-button');
                payButton.addEventListener('click', function () {
                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function (result) {
                            window.location.href = "{{ route('services.booking-success', $transaction->id) }}?transaction_status=settlement";
                        },
                        onPending: function (result) {
                            window.location.href = "{{ route('services.booking-success', $transaction->id) }}?transaction_status=pending";
                        },
                        onError: function (result) {
                            alert("Pembayaran gagal!");
                        },
                        onClose: function () {
                            alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                        }
                    });
                });
            </script>
        @endif
    @endsection
@endsection