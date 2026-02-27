@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Riwayat Aktivitas
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
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

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Berhasil!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-information fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Terjadi Kesalahan</h4>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">Filter Riwayat</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('history_membership') }}">
                        <div class="row g-3">
                            
                            {{-- ====================================================== --}}
                            {{-- ==== KODE DROPDOWN YANG DITAMBAHKAN UNTUK STAFF/OWNER ==== --}}
                            {{-- ====================================================== --}}
                            @if (auth()->user()->hasRole(['User:Owner', 'User:Staff']) && isset($members))                                <div class="col-md-3">
                                    <label class="form-label">Pilih Member</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">-- Pilih Member --</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}" {{ ($selectedUserId ?? null) == $member->id ? 'selected' : '' }}>
                                                {{ $member->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            {{-- ====================================================== --}}

                            <div class="col-md-3">
                                <label class="form-label">Jenis Aktivitas</label>
                                <select name="type" class="form-select">
                                    <option value="all" {{ ($type ?? 'all') == 'all' ? 'selected' : '' }}>Semua Aktivitas</option>
                                    <option value="memberships" {{ ($type ?? 'all') == 'memberships' ? 'selected' : '' }}>Membership</option>
                                    <option value="checkins" {{ ($type ?? 'all') == 'checkins' ? 'selected' : '' }}>Check-in</option>
                                    <option value="transactions" {{ ($type ?? 'all') == 'transactions' ? 'selected' : '' }}>Transaksi Produk</option>
                                    <option value="services" {{ ($type ?? 'all') == 'services' ? 'selected' : '' }}>Layanan Tambahan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control" value="{{ isset($dateFrom) && $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control" value="{{ isset($dateTo) && $dateTo ? $dateTo->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ki-duotone ki-magnifier fs-2"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sisa kode tidak ada perubahan, sama seperti yang Anda berikan --}}
            <div class="card mb-5">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ ($type ?? 'all') == 'all' ? 'active' : '' }}"
                                href="{{ route('history_membership', array_merge(request()->except('type'), ['type' => 'all'])) }}">
                                <i class="ki-duotone ki-chart-timeline fs-2 me-2"></i>
                                Semua Aktivitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($type ?? 'all') == 'memberships' ? 'active' : '' }}"
                                href="{{ route('history_membership', array_merge(request()->except('type'), ['type' => 'memberships'])) }}">
                                <i class="ki-duotone ki-badge fs-2 me-2"></i>
                                Membership
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($type ?? 'all') == 'checkins' ? 'active' : '' }}"
                                href="{{ route('history_membership', array_merge(request()->except('type'), ['type' => 'checkins'])) }}">
                                <i class="ki-duotone ki-login fs-2 me-2"></i>
                                Check-in
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($type ?? 'all') == 'transactions' ? 'active' : '' }}"
                                href="{{ route('history_membership', array_merge(request()->except('type'), ['type' => 'transactions'])) }}">
                                <i class="ki-duotone ki-basket fs-2 me-2"></i>
                                Transaksi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($type ?? 'all') == 'services' ? 'active' : '' }}"
                                href="{{ route('history_membership', array_merge(request()->except('type'), ['type' => 'services'])) }}">
                                <i class="ki-duotone ki-setting-2 fs-2 me-2"></i>
                                Layanan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            @if(($type ?? 'all') == 'all')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Timeline Aktivitas</h3>
                    </div>
                    <div class="card-body">
                        @if(!isset($timeline) || $timeline->isEmpty())
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-file-search fs-4x text-muted mb-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h4 class="text-muted">Belum ada aktivitas</h4>
                                <p class="text-muted">
                                    @if(auth()->user()->hasRole(['User:Owner', 'User:Staff']))
                                        Pilih seorang member untuk melihat riwayat aktivitasnya.
                                    @else
                                        Riwayat aktivitas Anda akan muncul di sini.
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach($timeline as $item)
                                    <div class="timeline-item">
                                        <div class="timeline-line w-40px"></div>
                                        <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                            <div class="symbol-label bg-light">
                                                @switch($item['type'])
                                                    @case('membership')
                                                        <i class="ki-duotone ki-badge fs-2 text-primary">
                                                            <span class="path1"></span><span class="path2"></span>
                                                        </i>
                                                        @break
                                                    @case('checkin')
                                                        <i class="ki-duotone ki-login fs-2 text-success">
                                                            <span class="path1"></span><span class="path2"></span>
                                                        </i>
                                                        @break
                                                    @case('transaction')
                                                        <i class="ki-duotone ki-basket fs-2 text-info">
                                                            <span class="path1"></span><span class="path2"></span>
                                                        </i>
                                                        @break
                                                    @case('service')
                                                        <i class="ki-duotone ki-setting-2 fs-2 text-warning">
                                                            <span class="path1"></span><span class="path2"></span>
                                                        </i>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                        <div class="timeline-content mb-10 mt-n1">
                                            <div class="pe-3 mb-5">
                                                <div class="fs-5 fw-semibold mb-2">{{ $item['title'] }}</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="text-muted me-2 fs-7">
                                                        {{ $item['date']->format('d M Y H:i') }}
                                                    </div>
                                                    @if($item['amount'])
                                                        <div class="text-primary fw-bold me-2 fs-7">
                                                            {{ $item['amount'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-muted mt-2">{{ $item['description'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @switch($type ?? 'all')
                                @case('memberships')
                                    Riwayat Membership
                                    @break
                                @case('checkins')
                                    Riwayat Check-in
                                    @break
                                @case('transactions')
                                    Riwayat Transaksi
                                    @break
                                @case('services')
                                    Riwayat Layanan
                                    @break
                            @endswitch
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(($type ?? 'all') == 'memberships' && (!isset($memberships) || $memberships->isEmpty()))
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-badge fs-4x text-muted mb-4">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <h4 class="text-muted">Belum ada membership</h4>
                                <p class="text-muted">Riwayat membership akan muncul di sini</p>
                            </div>
                        @elseif(($type ?? 'all') == 'checkins' && (!isset($checkinLogs) || $checkinLogs->isEmpty()))
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-login fs-4x text-muted mb-4">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <h4 class="text-muted">Belum ada check-in</h4>
                                <p class="text-muted">Riwayat check-in akan muncul di sini</p>
                            </div>
                        @elseif(($type ?? 'all') == 'transactions' && (!isset($transactions) || $transactions->isEmpty()))
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-basket fs-4x text-muted mb-4">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <h4 class="text-muted">Belum ada transaksi</h4>
                                <p class="text-muted">Riwayat transaksi akan muncul di sini</p>
                            </div>
                        @elseif(($type ?? 'all') == 'services' && (!isset($serviceTransactions) || $serviceTransactions->isEmpty()))
                             <div class="text-center py-10">
                                <i class="ki-duotone ki-setting-2 fs-4x text-muted mb-4">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <h4 class="text-muted">Belum ada layanan</h4>
                                <p class="text-muted">Riwayat layanan akan muncul di sini</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            @if(($type ?? 'all') == 'memberships')
                                                <th>Paket</th>
                                                <th>Tanggal Pembelian</th>
                                                <th>Status</th>
                                                <th>Tanggal Kadaluarsa</th>
                                            @elseif(($type ?? 'all') == 'checkins')
                                                <th>Tanggal Check-in</th>
                                                <th>Tanggal Check-out</th>
                                                <th>Durasi</th>
                                                <th>Status</th>
                                            @elseif(($type ?? 'all') == 'transactions')
                                                <th>Produk</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            @elseif(($type ?? 'all') == 'services')
                                                <th>Layanan</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(($type ?? 'all') == 'memberships' && isset($memberships))
                                            @foreach($memberships as $membership)
                                                <tr>
                                                    <td>{{ $membership->package?->name ?? 'Paket Tidak Ditemukan' }}</td>
                                                    <td>{{ $membership->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        @if($membership->status == 'active' && $membership->end_date->isFuture())
                                                            <span class="badge badge-light-success">Aktif</span>
                                                        @else
                                                            <span class="badge badge-light-danger">Kadaluarsa</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $membership->end_date->format('d M Y') }}</td>
                                                </tr>
                                            @endforeach
                                        @elseif(($type ?? 'all') == 'checkins' && isset($checkinLogs))
                                            @foreach($checkinLogs as $log)
                                                <tr>
                                                    <td>{{ $log->checkin_time->format('d M Y H:i') }}</td>
                                                    <td>{{ $log->checkout_time ? $log->checkout_time->format('d M Y H:i') : '-' }}</td>
                                                    <td>{{ $log->checkout_time ? $log->checkin_time->diffForHumans($log->checkout_time, true) : 'Sedang berlangsung' }}</td>
                                                    <td>
                                                        @if($log->checkout_time)
                                                            <span class="badge badge-light-primary">Selesai</span>
                                                        @else
                                                            <span class="badge badge-light-success">Aktif</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif(($type ?? 'all') == 'transactions' && isset($transactions))
                                            @foreach($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->product?->name ?? 'Produk Tidak Ditemukan' }}</td>
                                                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge badge-light-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif(($type ?? 'all') == 'services' && isset($serviceTransactions))
                                            @foreach($serviceTransactions as $service)
                                                <tr>
                                                    <td>{{ $service->service?->name ?? 'Layanan Tidak Ditemukan' }}</td>
                                                    <td>{{ $service->transaction_date->format('d M Y') }}</td>
                                                    <td>Rp {{ number_format($service->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge badge-light-{{ $service->status == 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($service->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if(($type ?? 'all') != 'all')
                                <div class="d-flex justify-content-center mt-5">
                                    @if(($type ?? 'all') == 'memberships' && isset($memberships))
                                        {{ $memberships->withQueryString()->links() }}
                                    @elseif(($type ?? 'all') == 'checkins' && isset($checkinLogs))
                                        {{ $checkinLogs->withQueryString()->links() }}
                                    @elseif(($type ?? 'all') == 'transactions' && isset($transactions))
                                        {{ $transactions->withQueryString()->links() }}
                                    @elseif(($type ?? 'all') == 'services' && isset($serviceTransactions))
                                        {{ $serviceTransactions->withQueryString()->links() }}
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tidak ada perubahan pada script, tapi tetap disertakan --}}
<script>
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const params = new URLSearchParams(window.location.search);
            params.set('type', url.searchParams.get('type'));
            window.location.search = params.toString();
        });
    });
</script>
@endpush