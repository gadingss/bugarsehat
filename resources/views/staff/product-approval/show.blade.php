@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title fw-bold">Detail Transaksi</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Detail Produk</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-60px symbol-circle me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="fas fa-box text-primary fs-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $transaction->product->name }}</h6>
                                    <span class="text-muted">{{ $transaction->product->category }}</span>
                                </div>
                            </div>
                            <table class="table table-borderless">
                <tr>
                    <td class="text-muted">Jumlah</td>
                    <td class="fw-bold">{{ $transaction->quantity }} unit</td>
                </tr>
                <tr>
                    <td class="text-muted">Harga Satuan</td>
                    <td class="fw-bold">Rp {{ number_format($transaction->product->getCurrentPrice(), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Total</td>
                    <td class="fw-bold text-primary fs-5">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Status</td>
                    <td class="fw-bold">{{ $transaction->status }}</td>
                </tr>
            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Informasi Pembayaran</h5>
                            <div class="alert alert-info">
                <h6 class="fw-bold">Informasi Pembayaran</h6>
                <p class="mb-2">Silakan transfer ke rekening berikut:</p>
                <div class="bg-light p-3 rounded">
                    <strong>Bank: BCA</strong><br>
                    <strong>No. Rekening: 1234567890</strong><br>
                <strong>Atas Nama: Bugar Sehat Gym</strong>
                </div>
            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <h5 class="fw-bold mb-3">Validasi Transaksi</h5>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check me-2"></i>Validasi
                    </button>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-times me-2"></i>Tolak
                    </button>
                </div>
            </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
