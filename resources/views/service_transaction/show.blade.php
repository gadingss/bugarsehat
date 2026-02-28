@extends('layouts.app')

@section('title', 'Detail Transaksi Layanan')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <div class="d-flex align-items-center">
                        @unless(auth()->user()->hasRole('User:Trainer'))
                            <a href="{{ route('service_transaction') }}" class="btn btn-sm btn-light-primary me-3">
                                <i class="ki-duotone ki-arrow-left fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Kembali
                            </a>
                        @endunless
                        <div>
                            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                Detail Transaksi Layanan
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                @unless(auth()->user()->hasRole('User:Trainer'))
                                    <li class="breadcrumb-item text-muted">
                                        <a href="{{ route('service_transaction') }}"
                                            class="text-muted text-hover-primary">Transaksi Layanan</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                    </li>
                                @endunless
                                <li class="breadcrumb-item text-muted">Detail</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Informasi Transaksi</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="fw-semibold text-gray-600">ID Transaksi</label>
                                        <p class="fw-bold fs-5">#{{ $serviceTransaction->id }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-semibold text-gray-600">Tanggal Transaksi</label>
                                        <p class="fw-bold">{{ $serviceTransaction->transaction_date->format('d F Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-semibold text-gray-600">Status</label>
                                        <p>
                                            @if($serviceTransaction->status == 'pending')
                                                <span class="badge badge-light-warning fs-7">Menunggu Konfirmasi</span>
                                            @elseif($serviceTransaction->status == 'scheduled')
                                                <span class="badge badge-light-info fs-7">Dijadwalkan</span>
                                            @elseif($serviceTransaction->status == 'completed')
                                                <span class="badge badge-light-success fs-7">Selesai</span>
                                            @elseif($serviceTransaction->status == 'cancelled')
                                                <span class="badge badge-light-danger fs-7">Dibatalkan</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="fw-semibold text-gray-600">Layanan</label>
                                        <p class="fw-bold fs-5">
                                            {{ $serviceTransaction->service->name ?? 'Layanan tidak tersedia' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-semibold text-gray-600">Jumlah Pembayaran</label>
                                        <p class="fw-bold fs-5 text-primary">Rp
                                            {{ number_format($serviceTransaction->amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="fw-semibold text-gray-600">Pelanggan</label>
                                        <p class="fw-bold">{{ $serviceTransaction->user->name ?? 'Tidak tersedia' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-semibold text-gray-600">Email</label>
                                        <p class="fw-bold">{{ $serviceTransaction->user->email ?? 'Tidak tersedia' }}</p>
                                    </div>
                                </div>

                                @if($serviceTransaction->scheduled_date)
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-gray-600">Jadwal Pelaksanaan</label>
                                            <p class="fw-bold">{{ $serviceTransaction->scheduled_date->format('d F Y H:i') }}
                                            </p>
                                        </div>
                                        @if($serviceTransaction->completed_date)
                                            <div class="col-md-6">
                                                <label class="fw-semibold text-gray-600">Tanggal Selesai</label>
                                                <p class="fw-bold">{{ $serviceTransaction->completed_date->format('d F Y H:i') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($serviceTransaction->payment_method)
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-gray-600">Metode Pembayaran</label>
                                            <p class="fw-bold">{{ ucfirst($serviceTransaction->payment_method) }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($serviceTransaction->notes)
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="fw-semibold text-gray-600">Catatan</label>
                                            <p class="fw-bold">{{ $serviceTransaction->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Aksi</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    @if($serviceTransaction->canCancel() && $serviceTransaction->user_id == Auth::id())
                                        <form action="{{ route('service_transaction.cancel', $serviceTransaction) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-100"
                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')">
                                                <i class="ki-duotone ki-cross-circle fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Batalkan Transaksi
                                            </button>
                                        </form>
                                    @endif
                                    @unless(auth()->user()->hasRole('User:Trainer'))
                                        <a href="{{ route('service_transaction') }}" class="btn btn-light">
                                            <i class="ki-duotone ki-arrow-left fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Kembali ke Daftar
                                        </a>
                                    @endunless
                                </div>
                            </div>
                        </div>

                        <div class="card mt-5">
                            <div class="card-header">
                                <h3 class="card-title">Ringkasan</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="fw-bold">Rp
                                        {{ number_format($serviceTransaction->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-gray-600">Status</span>
                                    <span class="fw-bold">
                                        @if($serviceTransaction->status == 'pending')
                                            Menunggu
                                        @elseif($serviceTransaction->status == 'scheduled')
                                            Dijadwalkan
                                        @elseif($serviceTransaction->status == 'completed')
                                            Selesai
                                        @elseif($serviceTransaction->status == 'cancelled')
                                            Dibatalkan
                                        @endif
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold text-primary fs-5">Rp
                                        {{ number_format($serviceTransaction->amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection