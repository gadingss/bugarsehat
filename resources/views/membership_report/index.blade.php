@extends('layouts.app') {{-- Ganti jika layout-mu berbeda --}}

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i> Laporan Membership
            </h4>
            {{-- Tombol Ekspor --}}
            <div class="btn-group">
                <a href="{{ route('laporan.membership.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                </a>
                <a href="{{ route('laporan.membership.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-file-excel text-success me-1"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Filter Form --}}
            <form method="GET" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>

            {{-- Tabel Laporan --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Member</th>
                            <th>Paket</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal Aktivasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->package->name ?? '-' }}</td>
                                <td class="text-center">
                                    @if($item->status == 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($item->status == 'expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="mb-0">Tidak ada data yang ditemukan.</p>
                                    <small>Coba ubah kriteria filter Anda.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Pastikan Anda sudah meload Font Awesome di layout utama Anda --}}
{{-- Jika belum, Anda bisa tambahkan link ini di layout utama atau di sini --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush