@extends('layouts.app')

@section('title', 'Pembelian Berhasil')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-flush shadow-sm">
                    <div class="card-body text-center py-10 px-10">
                        <div class="mb-6">
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-success">
                                    <i class="fas fa-check-circle text-success fs-3x"></i>
                                </div>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-3">Pembelian Berhasil!</h2>
                        <p class="text-muted mb-8 fs-6">
                            @if(request('transaction_status') == 'settlement' || $transaction->status == 'validated')
                                Terima kasih! Pembayaran Anda telah kami terima dan pesanan Anda sedang diproses.
                            @else
                                Pesanan Anda telah berhasil dibuat. Silakan selesaikan pembayaran dan tunggu validasi staff.
                            @endif
                        </p>

                        <div class="bg-light p-5 rounded mb-8 border border-dashed border-gray-300">
                            <h5 class="fw-bold mb-4 text-start">Ringkasan Transaksi</h5>
                            <div class="text-start">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Produk:</span>
                                    <span class="fw-bold">{{ $transaction->product->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Jumlah:</span>
                                    <span class="fw-bold">{{ $transaction->quantity }} unit</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Total Bayar:</span>
                                    <span class="fw-bold text-primary fs-5">Rp
                                        {{ number_format((float) $transaction->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    @if($transaction->status == 'validated')
                                        <span class="badge badge-light-success">Divalidasi</span>
                                    @else
                                        <span class="badge badge-light-warning">Menunggu Validasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-shopping-cart me-2"></i>Lanjut Belanja
                            </a>
                            <a href="{{ route('products.my-products') }}" class="btn btn-light-primary">
                                <i class="fas fa-list-ul me-2"></i>Lihat Riwayat Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection