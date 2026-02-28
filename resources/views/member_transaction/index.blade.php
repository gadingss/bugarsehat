@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('home') }}" class="btn btn-sm btn-light-primary me-3">
                            <i class="ki-duotone ki-arrow-left fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Kembali
                        </a>
                        <div>
                            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                Riwayat Transaksi
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Riwayat Transaksi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <!-- Statistics Cards -->
                <div class="row g-5 g-xl-8 mb-5">
                    <div class="col-xl-3">
                        <div class="card card-xl-stretch">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-primary svg-icon-3x ms-n1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                            d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L12 2.725L22 6.725Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $statistics['total_transactions'] }}
                                </div>
                                <div class="fw-semibold text-gray-400">Total Transaksi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card card-xl-stretch">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-success svg-icon-3x ms-n1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M13.0079 2.6L15.7079 7.2L21.0079 8.4C21.9079 8.6 22.3079 9.7 21.7079 10.4L18.1079 14.4L18.6079 19.8C18.7079 20.7 17.7079 21.4 16.9079 21L12.0079 18.8L7.10785 21C6.20785 21.4 5.30786 20.7 5.40786 19.8L5.90786 14.4L2.30785 10.4C1.70785 9.7 2.00786 8.6 3.00786 8.4L8.30785 7.2L11.0079 2.6C11.3079 1.8 12.5079 1.8 13.0079 2.6Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $statistics['validated_count'] }}</div>
                                <div class="fw-semibold text-gray-400">Transaksi Berhasil</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card card-xl-stretch">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-warning svg-icon-3x ms-n1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z"
                                            fill="currentColor" />
                                        <path
                                            d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $statistics['pending_count'] }}</div>
                                <div class="fw-semibold text-gray-400">Menunggu Validasi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card card-xl-stretch">
                            <div class="card-body">
                                <span class="svg-icon svg-icon-info svg-icon-3x ms-n1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 11.7127L10 14.1127L22 11.7127L14 9.31274L2 11.7127Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                            d="M20.9 7.91274L2 11.7127V6.81275C2 6.11275 2.50001 5.61274 3.10001 5.51274L20.6 2.01274C21.3 1.91274 22 2.41273 22 3.11273V6.61273C22 7.21273 21.5 7.71274 20.9 7.91274ZM22 16.6127V11.7127L3.10001 15.5127C2.50001 15.6127 2 16.1127 2 16.8127V20.3127C2 21.0127 2.69999 21.6128 3.39999 21.4128L20.9 17.9128C21.5 17.8128 22 17.2127 22 16.6127Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">Rp
                                    {{ number_format($statistics['total_amount'], 0, ',', '.') }}
                                </div>
                                <div class="fw-semibold text-gray-400">Total Pembayaran</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card mb-5">
                    <div class="card-body">
                        <form method="GET" action="{{ route('member_transaction') }}" class="form">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Cari Produk</label>
                                    <input type="text" name="search" class="form-control" placeholder="Nama produk..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Menunggu</option>
                                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>
                                            Berhasil</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ki-duotone ki-magnifier fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Filter
                                        </button>
                                        <a href="{{ route('member_transaction') }}" class="btn btn-secondary">
                                            <i class="ki-duotone ki-arrows-circle fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <h3>Daftar Transaksi</h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-light-primary me-3" onclick="window.print()">
                                    <i class="ki-duotone ki-printer fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-50px">#</th>
                                        <th class="min-w-120px">Tanggal</th>
                                        <th class="min-w-200px">Produk</th>
                                        <th class="min-w-120px">Jumlah</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-150px">Divalidasi Oleh</th>
                                        <th class="min-w-100px text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $index => $transaction)
                                        <tr>
                                            <td>
                                                <span class="text-dark fw-bold text-hover-primary fs-6">
                                                    {{ $transactions->firstItem() + $index }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-bold text-hover-primary d-block fs-6">
                                                    {{ $transaction->transaction_date->format('d M Y') }}
                                                </span>
                                                <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                    {{ $transaction->transaction_date->format('H:i') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-3">
                                                        <span class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-cube-2 fs-2x text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span class="text-dark fw-bold text-hover-primary fs-6">
                                                            {{ $transaction->item->name ?? 'Produk/Paket tidak tersedia' }}
                                                        </span>
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                            ID: #{{ $transaction->id }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-bold text-hover-primary fs-6">
                                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->status == 'pending')
                                                    <span class="badge badge-light-warning">Menunggu</span>
                                                @elseif($transaction->status == 'validated')
                                                    <span class="badge badge-light-success">Berhasil</span>
                                                @elseif($transaction->status == 'cancelled')
                                                    <span class="badge badge-light-danger">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->validator)
                                                    <span class="text-dark fw-bold text-hover-primary fs-6">
                                                        {{ $transaction->validator->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-light btn-active-light-primary"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal{{ $transaction->id }}">
                                                    <i class="ki-duotone ki-eye fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal{{ $transaction->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Transaksi #{{ $transaction->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-4">
                                                            <label class="fw-semibold fs-6 text-gray-600">Tanggal
                                                                Transaksi</label>
                                                            <p class="fw-bold fs-6">
                                                                {{ $transaction->transaction_date->format('d F Y H:i') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label class="fw-semibold fs-6 text-gray-600">Produk/Paket</label>
                                                            <p class="fw-bold fs-6">
                                                                {{ $transaction->item->name ?? 'Produk/Paket tidak tersedia' }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label class="fw-semibold fs-6 text-gray-600">Jumlah
                                                                Pembayaran</label>
                                                            <p class="fw-bold fs-6">Rp
                                                                {{ number_format($transaction->amount, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label class="fw-semibold fs-6 text-gray-600">Status</label>
                                                            <p>
                                                                @if($transaction->status == 'pending')
                                                                    <span class="badge badge-light-warning">Menunggu Validasi</span>
                                                                @elseif($transaction->status == 'validated')
                                                                    <span class="badge badge-light-success">Berhasil</span>
                                                                @elseif($transaction->status == 'cancelled')
                                                                    <span class="badge badge-light-danger">Dibatalkan</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        @if($transaction->validator)
                                                            <div class="mb-4">
                                                                <label class="fw-semibold fs-6 text-gray-600">Divalidasi
                                                                    Oleh</label>
                                                                <p class="fw-bold fs-6">{{ $transaction->validator->name }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ki-duotone ki-document fs-5x text-muted mb-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <span class="text-muted fs-4">Tidak ada data transaksi</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-5">
                                <div class="text-muted">
                                    Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari
                                    {{ $transactions->total() }} transaksi
                                </div>
                                {{ $transactions->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        @media print {

            .app-header,
            .app-sidebar,
            .app-toolbar,
            .card-toolbar,
            .btn,
            .form,
            .pagination {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection