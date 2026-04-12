@extends('layouts.app')

@section('title', 'Pembayaran Membership')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0 text-white"><i class="fas fa-credit-card me-2"></i> Pembayaran Membership</h4>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-file-invoice-dollar text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">Invoice: {{ $transaction->invoice_id }}</h5>
                    <h2 class="mb-4 fw-bolder text-dark">Total Pembayaran: Rp {{ number_format($transaction->amount, 0, ',', '.') }}</h2>
                    <p class="text-muted mb-5 fs-5">Silakan selesaikan pembayaran Anda untuk mengaktifkan membership.</p>
                    
                    <button id="pay-button" class="btn btn-primary fw-bold" style="font-size: 1.2rem; padding: 15px 30px; letter-spacing: 1px;">
                        <i class="fas fa-wallet me-2"></i> BAYAR SEKARANG (MIDTRANS)
                    </button>
                    <br><br>
                    <a href="{{ route('activation_order') }}" class="btn btn-light mt-4 px-4"><i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
    document.getElementById('pay-button').onclick = function () {
        // SnapToken acquired from previous step
        snap.pay('{{ $transaction->snap_token }}', {
            // Optional
            onSuccess: function(result) {
                alert("Pembayaran berhasil! Membership Anda aktif.");
                window.location.href = "{{ route('activation_order') }}";
            },
            // Optional
            onPending: function(result) {
                alert("Menunggu pembayaran Anda!");
                window.location.href = "{{ route('activation_order') }}";
            },
            // Optional
            onError: function(result) {
                alert("Pembayaran gagal!");
            },
            onClose: function () {
                console.log('User closed the popup without finishing the payment');
            }
        });
    };
</script>
@endsection
