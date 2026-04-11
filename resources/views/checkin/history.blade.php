@extends('layouts.app')

@section('title', 'Riwayat Check-in')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Riwayat Check-in
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('checkin.index') }}" class="text-muted text-hover-primary">Check-in</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Riwayat</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="row g-5 g-xl-6 mb-5">
                <div class="col-6 col-md-3">
                    <div class="card card-flush h-100">
                        <div class="card-body py-5 text-center">
                            <i class="ki-duotone ki-calendar-tick fs-3x text-primary mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div class="fs-2hx fw-bold mb-1">{{ $stats['today'] ?? 0 }}</div>
                            <div class="fs-6 fw-semibold text-gray-400">Hari Ini</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-flush h-100">
                        <div class="card-body py-5 text-center">
                            <i class="ki-duotone ki-calendar-8 fs-3x text-success mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                            <div class="fs-2hx fw-bold mb-1">{{ $stats['this_week'] ?? 0 }}</div>
                            <div class="fs-6 fw-semibold text-gray-400">Minggu Ini</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-flush h-100">
                        <div class="card-body py-5 text-center">
                            <i class="ki-duotone ki-calendar-add fs-3x text-warning mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                            <div class="fs-2hx fw-bold mb-1">{{ $stats['this_month'] ?? 0 }}</div>
                            <div class="fs-6 fw-semibold text-gray-400">Bulan Ini</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-flush h-100">
                        <div class="card-body py-5 text-center">
                            <i class="ki-duotone ki-check-circle fs-3x text-info mb-3"><span class="path1"></span><span class="path2"></span></i>
                            <div class="fs-2hx fw-bold mb-1">{{ $stats['total_visits'] ?? 0 }}</div>
                            <div class="fs-6 fw-semibold text-gray-400">Total Kunjungan</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Riwayat Check-in</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 m-0">
                            <thead class="bg-light">
                                <tr class="fw-bold text-muted text-uppercase fs-7">
                                    <th class="ps-5 min-w-150px">Waktu Kedatangan</th>
                                    <th class="min-w-150px">Waktu Kepulangan</th>
                                    <th class="min-w-150px">Durasi Session</th>
                                    <th class="min-w-150px text-end pe-5">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checkins as $log)
                                    <tr>
                                        <td class="ps-5">
                                            <span class="text-dark fw-bold text-hover-primary d-block fs-6">
                                                {{ $log->checkin_time->format('d M Y') }}
                                            </span>
                                            <span class="text-muted fw-semibold d-block fs-7">
                                                {{ $log->checkin_time->format('H:i') }} WIB
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->checkout_time)
                                                <span class="text-dark fw-bold text-hover-primary d-block fs-6">
                                                    {{ $log->checkout_time->format('d M Y') }}
                                                </span>
                                                <span class="text-muted fw-semibold d-block fs-7">
                                                    {{ $log->checkout_time->format('H:i') }} WIB
                                                </span>
                                            @else
                                                <span class="text-muted fw-bold d-block fs-6">-</span>
                                                <span class="text-muted fw-semibold d-block fs-7">Sedang Berlatih</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold d-block fs-6">
                                                {{ $log->checkout_time ? $log->checkin_time->diffForHumans($log->checkout_time, true) : 'Belum Selesai' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-5">
                                            @if($log->checkout_time)
                                                <span class="badge py-3 px-4 fs-7 badge-light-primary">
                                                    <i class="fas fa-check-circle fs-5 me-2 text-primary"></i> Selesai
                                                </span>
                                            @else
                                                <span class="badge py-3 px-4 fs-7 badge-light-success">
                                                    <i class="fa fa-spinner fa-spin fs-5 me-2 text-success"></i> Aktif Berlatih
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10">
                                            <i class="ki-duotone ki-login fs-3x text-muted mb-4"><span class="path1"></span><span class="path2"></span></i>
                                            <h4 class="text-muted">Belum ada aktivitas</h4>
                                            <p class="text-muted">Riwayat check-in Anda akan muncul di sini</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($checkins->hasPages())
                    <div class="card-footer d-flex justify-content-end p-5">
                        {{ $checkins->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
