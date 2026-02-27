@extends('layouts.app')

@section('title', 'Pembayaran Produk')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-flush shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Pembayaran Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="fw-bold mb-3">Detail Produk</h5>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-60px symbol-circle me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="fas fa-box text-primary fs-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $transaction->product->name }}</h6>
                                        <span class="text-muted fs-7">{{ $transaction->product->category }}</span>
                                    </div>
                                </div>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">Jumlah</td>
                                        <td class="fw-bold text-end">{{ $transaction->quantity }} unit</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Harga Satuan</td>
                                        <td class="fw-bold text-end">Rp
                                            {{ number_format($transaction->product->getCurrentPrice(), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <hr class="my-1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-primary fs-4 text-end">Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Konfirmasi Pembayaran</h5>

                                @if(isset($snapToken) && $snapToken)
                                    <div class="mb-8">
                                        <div class="alert alert-primary d-flex align-items-center p-5 mb-5">
                                            <i class="fas fa-credit-card fs-2hx text-primary me-4"></i>
                                            <div class="d-flex flex-column">
                                                <h4 class="fw-bold text-primary">Pembayaran Online</h4>
                                                <span class="fs-7">Bayar instan via QRIS, Virtual Account, atau Bank
                                                    Transfer.</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success w-100 py-4 fs-5 fw-bold shadow-sm mb-4"
                                            id="pay-button">
                                            <i class="fas fa-wallet me-2"></i>Bayar dengan Midtrans
                                        </button>
                                        <div class="separator separator-content border-gray-300 my-8">
                                            <span class="w-100px fw-bold text-muted fs-8">ATAU MANUAL</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="alert alert-info py-3 px-4 mb-5">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-university me-2 text-info"></i>
                                        <h6 class="fw-bold mb-0">Transfer Bank Manual</h6>
                                    </div>
                                    <div class="p-2 rounded bg-white bg-opacity-50 fs-7">
                                        <div>Bank: <strong>BCA</strong></div>
                                        <div>No. Rekening: <strong>1234567890</strong></div>
                                        <div>Atas Nama: <strong>Bugar Sehat Gym</strong></div>
                                    </div>
                                </div>

                                <form action="{{ route('products.confirm-payment', $transaction->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Upload Bukti Transfer Manual</label>
                                        <input type="file" class="form-control" name="payment_proof" accept="image/*,.pdf"
                                            required>
                                        <div class="form-text fs-8 text-muted">Format: JPG, PNG, PDF (Max 2MB)</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                        <i class="fas fa-check-circle me-2"></i>Kirim Bukti Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-4 text-center">
                        <p class="text-gray-600 fs-7 mb-0">
                            <i class="fas fa-shield-alt me-1"></i> Transaksi Anda aman & terverifikasi.
                        </p>
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
                            window.location.href = "{{ route('products.success', $transaction->id) }}?transaction_status=settlement";
                        },
                        onPending: function (result) {
                            window.location.href = "{{ route('products.success', $transaction->id) }}?transaction_status=pending";
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