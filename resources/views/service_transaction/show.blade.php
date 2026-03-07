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
                                            {{ $serviceTransaction->service->name ?? 'Layanan tidak tersedia' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-semibold text-gray-600">Jumlah Pembayaran</label>
                                        <p class="fw-bold fs-5 text-primary">Rp
                                            {{ number_format($serviceTransaction->amount, 0, ',', '.') }}
                                        </p>
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

                        {{-- START: Sesi Latihan --}}
                        @if($serviceTransaction->status == 'scheduled' || $serviceTransaction->status == 'completed')
                            <div class="card mb-5 mt-5">
                                <div class="card-header align-items-center d-flex justify-content-between">
                                    <h3 class="card-title">Sesi Latihan</h3>
                                    @if((auth()->user()->hasRole('User:Staff') || auth()->user()->hasRole('User:Owner')) && $serviceTransaction->status != 'completed')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_add_session">
                                            <i class="ki-duotone ki-plus fs-2"></i> Tambah Sesi
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4 min-w-50px rounded-start">Sesi</th>
                                                    <th class="min-w-150px">Topik Latihan</th>
                                                    <th class="min-w-150px">Jadwal</th>
                                                    <th class="min-w-100px">Status</th>
                                                    <th class="min-w-100px text-end rounded-end border-end-0 pe-4">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($serviceTransaction->serviceSessions as $session)
                                                    <tr>
                                                        <td class="ps-4">
                                                            <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">Sesi
                                                                {{ $session->session_number }}</span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="text-dark fw-bold d-block mb-1 fs-6">{{ $session->topic }}</span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="text-muted fw-semibold text-muted d-block fs-7">{{ $session->scheduled_date ? $session->scheduled_date->format('d M Y H:i') : '-' }}</span>
                                                        </td>
                                                        <td>
                                                            @if($session->status == 'pending')
                                                                <span class="badge badge-light-warning">Menunggu</span>
                                                            @elseif($session->status == 'attended')
                                                                <span class="badge badge-light-success">Hadir</span>
                                                            @elseif($session->status == 'missed')
                                                                <span class="badge badge-light-danger">Tidak Hadir</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            @if((auth()->user()->hasRole('User:Staff') || auth()->user()->hasRole('User:Owner')) && $session->status == 'pending')
                                                                <button
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#kt_modal_edit_session_{{ $session->id }}">
                                                                    <i class="ki-duotone ki-pencil fs-2"></i>
                                                                </button>
                                                                <form
                                                                    action="{{ route('service_transaction.sessions.destroy', $session->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                                                        onclick="return confirm('Hapus sesi ini?')">
                                                                        <i class="ki-duotone ki-trash fs-2"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    {{-- Edit Session Modal --}}
                                                    @if((auth()->user()->hasRole('User:Staff') || auth()->user()->hasRole('User:Owner')) && $session->status == 'pending')
                                                        <div class="modal fade" id="kt_modal_edit_session_{{ $session->id }}"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered mw-650px">
                                                                <div class="modal-content">
                                                                    <form
                                                                        action="{{ route('service_transaction.sessions.update', $session->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-header">
                                                                            <h2 class="fw-bold">Edit Sesi {{ $session->session_number }}
                                                                            </h2>
                                                                            <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                                                data-bs-dismiss="modal">
                                                                                <i class="ki-duotone ki-cross fs-1"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-body py-10 px-lg-17">
                                                                            <div class="fv-row mb-7">
                                                                                <label class="required fs-6 fw-semibold mb-2">Topik
                                                                                    Latihan</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-solid" name="topic"
                                                                                    value="{{ $session->topic }}" required />
                                                                            </div>
                                                                            <div class="fv-row mb-7">
                                                                                <label class="required fs-6 fw-semibold mb-2">Tanggal &
                                                                                    Waktu</label>
                                                                                <input type="datetime-local"
                                                                                    class="form-control form-control-solid"
                                                                                    name="scheduled_date"
                                                                                    value="{{ $session->scheduled_date ? $session->scheduled_date->format('Y-m-d\TH:i') : '' }}"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer flex-center">
                                                                            <button type="button" data-bs-dismiss="modal"
                                                                                class="btn btn-light me-3">Batal</button>
                                                                            <button type="submit" class="btn btn-primary">
                                                                                <span class="indicator-label">Simpan Perubahan</span>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">Belum ada sesi latihan yang
                                                            dijadwalkan.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- END: Sesi Latihan --}}
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
                                                <i class="ki-duotone ki-cross-circle fs-2"></i> Batalkan Transaksi
                                            </button>
                                        </form>
                                    @endif
                                    @unless(auth()->user()->hasRole('User:Trainer'))
                                        <a href="{{ route('service_transaction') }}" class="btn btn-light">
                                            <i class="ki-duotone ki-arrow-left fs-2"></i> Kembali ke Daftar
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

    {{-- Add Session Modal --}}
    @if((auth()->user()->hasRole('User:Staff') || auth()->user()->hasRole('User:Owner')) && $serviceTransaction->status != 'completed')
        <div class="modal fade" id="kt_modal_add_session" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <form action="{{ route('service_transaction.sessions.store', $serviceTransaction->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h2 class="fw-bold">Tambah Sesi Latihan</h2>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"></i>
                            </div>
                        </div>
                        <div class="modal-body py-10 px-lg-17">
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold mb-2">Topik Latihan</label>
                                <input type="text" class="form-control form-control-solid" name="topic"
                                    placeholder="Contoh: Cardio Basic" required />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold mb-2">Tanggal & Waktu</label>
                                <input type="datetime-local" class="form-control form-control-solid" name="scheduled_date"
                                    required />
                            </div>
                        </div>
                        <div class="modal-footer flex-center">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection