@extends('layouts.app') {{-- Pastikan path ini benar untuk proyek Anda --}}

{{-- Tambahkan section untuk style kustom, jika layout Anda mendukungnya --}}
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" xintegrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Style kustom untuk tampilan yang lebih poles */
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }
        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-group .btn {
            margin-left: 10px;
        }
        .total-income-card {
            border-left: 5px solid #17a2b8; /* Warna highlight untuk kartu total */
        }
        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            border-radius: .5rem;
            border: none;
        }
    </style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <!-- Card Utama untuk Laporan -->
            <div class="card">
                <div class="card-header card-header-flex bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Laporan Pemasukan
                    </h4>
                    <!-- Tombol Ekspor -->
                    <div class="btn-group">
                        {{-- 
                            DIPERBAIKI: 
                            Nama route disesuaikan dengan definisi di file web.php 
                        --}}
                        <a href="{{ route('excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <a href="{{ route('pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Bagian Filter -->
                    <div class="card mb-4">
                        <div class="card-body bg-light">
                            <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                            {{-- Aksi form menunjuk ke route index utama --}}
                                <form method="GET" action="{{ route('income_report') }}" class="row g-3 align-items-end">                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Dari Tanggal</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-1"></i> Tampilkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Pemasukan -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Deskripsi</th>
                                    <th scope="col" class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomes as $index => $income)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($income->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td>{{ $income->description }}</td>
                                        <td class="text-end">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <p class="mb-0 text-muted">Tidak ada data pemasukan untuk periode yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan Total Pemasukan -->
                    <div class="row mt-4 justify-content-end">
                        <div class="col-md-5">
                            <div class="card total-income-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Total Pemasukan</h5>
                                        <h4 class="mb-0 fw-bold text-info">
                                            Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- End Card Body -->
            </div> <!-- End Main Card -->

        </div>
    </div>
</div>
@endsection
