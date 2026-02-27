@extends('layouts.app')

@section('title', 'Manajemen Aktivasi')

@section('content')
<style>
    /* Background container */
    .custom-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
    /* Table header dark navy */
    .table thead th {
        background-color: #0b0c1a !important;
        color: white;
        border: none !important;
    }
    /* Table body subtle borders */
    .table tbody tr {
        border-bottom: 1px solid #dee2e6;
    }
    /* Input group icon style */
    .input-group-text {
        background-color: white;
        border-right: 0;
    }
    .form-control.date-input {
        border-left: 0;
    }
    /* Adjust filter button height */
    .btn-filter {
        height: 38px;
        margin-top: 0;
    }
    /* Tab styling */
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: #0b0c1a;
        color: white;
        border-color: #0b0c1a;
    }
    .btn-group-actions {
        display: flex;
        gap: 5px;
    }
    .badge-type {
        font-size: 0.75em;
    }
</style>

<div class="container mt-4 custom-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Aktivasi</h3>
        <div>
            @if(auth()->user()->hasRole('User:Member'))
                <a href="{{ route('activation_order.extension.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Perpanjangan
                </a>
                <a href="{{ route('activation_order.application.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Pengajuan Baru
                </a>
            @elseif(auth()->user()->hasRole(['User:Staff', 'User:Owner']))
                <a href="{{ route('activation_order.extension.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Perpanjangan
                </a>
                <a href="{{ route('activation_order.application.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Pengajuan Baru
                </a>
            @endif
        </div>
    </div>

    {{-- Navigasi Tab --}}
    <ul class="nav nav-tabs mb-4" id="membershipTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab == 'activation' ? 'active' : '' }}" 
               href="{{ route('activation_order', ['tab' => 'activation']) }}">
                Aktivasi/Pembelian
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab == 'extension' ? 'active' : '' }}" 
               href="{{ route('activation_order', ['tab' => 'extension']) }}">
                Perpanjangan
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab == 'application' ? 'active' : '' }}" 
               href="{{ route('activation_order', ['tab' => 'application']) }}">
                Pengajuan Baru
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab == 'product' ? 'active' : '' }}" 
               href="{{ route('activation_order', ['tab' => 'product']) }}">
                Produk
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab == 'service' ? 'active' : '' }}" 
               href="{{ route('activation_order',['tab' => 'service']) }}">
                Layanan
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- =================================================================== --}}
    {{-- KONTEN UNTUK TAB MEMBERSHIP (AKTIVASI, PERPANJANGAN, PENGAJUAN)     --}}
    {{-- =================================================================== --}}
    @if(in_array($activeTab, ['activation', 'extension', 'application']))
    <form method="GET" action="{{ route('activation_order') }}" class="mb-4">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <input type="text" name="member_name" class="form-control" placeholder="Nama Member" value="{{ request('member_name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="package_name" class="form-control" placeholder="Nama Paket" value="{{ request('package_name') }}">
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">Status Pembayaran</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="validated" {{ request('payment_status') == 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="activation_status" class="form-select">
                    <option value="">Status Aktivasi</option>
                    <option value="inactive" {{ request('activation_status') == 'inactive' ? 'selected' : '' }}>Belum Aktif</option>
                    <option value="active" {{ request('activation_status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="cancelled" {{ request('activation_status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="expired" {{ request('activation_status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100 btn-filter">Filter</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Member</th>
                    <th>Paket Membership</th>
                    <th>Tipe</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Selesai</th>
                    <th>Sisa Kunjungan</th>
                    <th>Tgl Pembelian</th>
                    <th>Status Bayar</th>
                    <th>Status Aktivasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberships as $membership)
                <tr>
                    <td>{{ $membership->user->name }}</td>
                    <td>{{ $membership->package->name }}</td>
                    <td><span class="badge badge-type @if($membership->type == 'application') bg-info @elseif($membership->type == 'extension') bg-warning @else bg-secondary @endif">{{ ucfirst($membership->type) }}</span></td>
                    <td>{{ $membership->start_date ? \Carbon\Carbon::parse($membership->start_date)->format('d M Y') : '-' }}</td>
                    <td>{{ $membership->end_date ? \Carbon\Carbon::parse($membership->end_date)->format('d M Y') : '-' }}</td>
                    <td>{{ $membership->remaining_visits ?? 0 }}</td>
                    <td>{{ $membership->created_at->format('d M Y') }}</td>
                    <td>
                        @if($membership->transaction)
                            @if($membership->transaction->status == 'pending') <span class="badge bg-warning">Pending</span>
                            @elseif($membership->transaction->status == 'validated') <span class="badge bg-success">Tervalidasi</span>
                            @elseif($membership->transaction->status == 'failed') <span class="badge bg-danger">Gagal</span>
                            @else <span class="badge bg-secondary">{{ $membership->transaction->status }}</span>
                            @endif
                        @else <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        @if($membership->status == 'active') <span class="badge bg-success">Aktif</span>
                        @elseif($membership->status == 'inactive') <span class="badge bg-warning">Belum Aktif</span>
                        @elseif($membership->status == 'cancelled') <span class="badge bg-danger">Dibatalkan</span>
                        @elseif($membership->status == 'expired') <span class="badge bg-secondary">Kadaluarsa</span>
                        @else <span class="badge bg-secondary">{{ $membership->status }}</span>
                        @endif
                    </td>
                    <td>
                        {{-- ============================================= --}}
                        {{--               TOMBOL AKSI CRUD                --}}
                        {{-- ============================================= --}}
                        <div class="btn-group-actions">
                            @if(auth()->user()->hasRole(['User:Staff', 'User:Owner']))
                                {{-- Tombol Validasi Pembayaran --}}
                                @if($membership->transaction && $membership->transaction->status == 'pending')
                                    <form action="{{ route('activation_order.validate_payment', $membership->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info" title="Validasi Pembayaran" onclick="return confirm('Validasi pembayaran ini?')"><i class="fas fa-check-circle"></i></button>
                                    </form>
                                @endif

                                {{-- Tombol Aktivasi Membership --}}
                                @if($membership->transaction && $membership->transaction->status == 'validated' && $membership->status == 'inactive')
                                    <form action="{{ route('activation_order.activate_membership', $membership->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Aktifkan Membership" onclick="return confirm('Aktifkan membership ini?')"><i class="fas fa-user-check"></i></button>
                                    </form>
                                @endif
                                
                                {{-- Tombol Edit --}}
                                <a href="{{ route('activation_order.edit', $membership->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('activation_order.destroy', $membership->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center">Tidak ada data membership.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $memberships->withQueryString()->links() }}

    {{-- =================================================================== --}}
    {{-- KONTEN UNTUK TAB PRODUK                                           --}}
    {{-- =================================================================== --}}
    @elseif($activeTab == 'product')
    <div class="table-responsive">
        <p>Tabel ini menampilkan daftar pengajuan produk oleh member untuk disetujui.</p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Member</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->user->name ?? 'N/A' }}</td>
                    <td>{{ $product->name }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->created_at->format('d M Y') }}</td>
                    <td>
                        @if($product->status == 'approved') <span class="badge bg-success">Disetujui</span>
                        @elseif($product->status == 'rejected') <span class="badge bg-danger">Ditolak</span>
                        @else <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group-actions">
                            @if($product->status == 'pending')
                            <form action="{{ route('product.approve', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui pengajuan ini?')" title="Setujui"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ route('product.reject', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Tolak pengajuan ini?')" title="Tolak"><i class="fas fa-thumbs-down"></i></button>
                            </form>
                            @endif
                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data produk yang memerlukan persetujuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $products->withQueryString()->links() }}

    {{-- =================================================================== --}}
    {{-- KONTEN UNTUK TAB LAYANAN                                          --}}
    {{-- =================================================================== --}}
    @elseif($activeTab == 'service')
    <div class="table-responsive">
        <p>Tabel ini menampilkan daftar pengajuan layanan oleh member untuk disetujui.</p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Member</th>
                    <th>Nama Layanan</th>
                    <th>Harga</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td>{{ $service->user->name ?? 'N/A' }}</td>
                    <td>{{ $service->name }}</td>
                    <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                    <td>{{ $service->created_at->format('d M Y') }}</td>
                    <td>
                        @if($service->status == 'approved') <span class="badge bg-success">Disetujui</span>
                        @elseif($service->status == 'rejected') <span class="badge bg-danger">Ditolak</span>
                        @else <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group-actions">
                             @if($service->status == 'pending')
                            <form action="{{ route('service.approve', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui pengajuan ini?')" title="Setujui"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ route('service.reject', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Tolak pengajuan ini?')" title="Tolak"><i class="fas fa-thumbs-down"></i></button>
                            </form>
                            @endif
                            <a href="{{ route('service.edit', $service->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('service.destroy', $service->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data layanan yang memerlukan persetujuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $services->withQueryString()->links() }}
    @endif
</div>
@endsection
