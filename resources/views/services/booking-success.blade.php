@extends('layouts.app')

@section('title', 'Booking Berhasil')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-flush shadow-sm">
                    <div class="card-body text-center py-10 px-10">
                        <div class="mb-6">
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-success">
                                    <i class="fas fa-calendar-check text-success fs-3x"></i>
                                </div>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-3">Booking Berhasil!</h2>
                        <p class="text-muted mb-8 fs-6">
                            @if($transaction->service->price > 0)
                                Terima kasih! Bukti pembayaran Anda telah kami terima. Staff akan segera memvalidasi transaksi
                                Anda dalam waktu 24 jam.
                            @else
                                Booking Anda telah berhasil dikonfirmasi. Sampai jumpa di klub!
                            @endif
                        </p>

                        <div class="bg-light p-5 rounded mb-8 border border-dashed border-gray-300">
                            <h5 class="fw-bold mb-4 text-start">Ringkasan Booking</h5>
                            <div class="text-start">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Layanan:</span>
                                    <span class="fw-bold">{{ $transaction->service->name }}</span>
                                </div>
                                @if($transaction->trainer)
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Trainer:</span>
                                        <span class="fw-bold">{{ $transaction->trainer->name }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Jadwal:</span>
                                    <span
                                        class="fw-bold text-dark">{{ $transaction->scheduled_date->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Status Transaksi:</span>
                                    <span
                                        class="badge {{ $transaction->status == 'scheduled' ? 'badge-light-success' : 'badge-light-warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="{{ route('services.my-bookings') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-list-ul me-2"></i>Lihat Jadwal Saya
                            </a>
                            <a href="{{ route('services.index') }}" class="btn btn-light-primary">
                                <i class="fas fa-dumbbell me-2"></i>Explore Layanan Lain
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection