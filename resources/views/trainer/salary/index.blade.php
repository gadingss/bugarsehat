@extends('layouts.app')

@section('title', 'Penggajian Saya')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="card card-flush shadow-sm">
        <div class="card-header bg-primary pt-7 pb-5">
            <h3 class="card-title align-items-start flex-column text-white">
                <span class="card-label fw-bold fs-3 mb-1"><i class="fas fa-money-check-alt me-2 text-white"></i>Penggajian Saya</span>
                <span class="text-white opacity-75 mt-1 fw-semibold fs-7">Akumulasi pendapatan Anda dari transaksi layanan member</span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex align-items-center bg-white bg-opacity-25 rounded px-4 py-2 text-white">
                    <span class="fs-6 fw-bold me-2">Total Pendapatan:</span>
                    <span class="fs-4 fw-bolder">Rp {{ number_format($totalSalary, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div class="card-body py-5">
            <!-- Filter Section -->
            <form method="GET" action="{{ route('trainer.salary.index') }}" class="mb-6 d-flex flex-wrap align-items-center gap-3 bg-light rounded p-4 border border-dashed border-gray-300">
                <div class="d-flex align-items-center fw-bold text-gray-700 me-2">
                    <i class="fas fa-filter text-primary me-2"></i> Filter Bulan:
                </div>
                <select name="month" class="form-select form-select-sm w-150px form-select-solid">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ $selectedMonth == sprintf('%02d', $i) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select form-select-sm w-150px form-select-solid">
                    @for($i=now()->year - 2; $i<=now()->year + 1; $i++)
                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search me-1"></i> Terapkan
                </button>
                @if(request()->has('month') || request()->has('year'))
                    <a href="{{ route('trainer.salary.index') }}" class="btn btn-sm btn-light-danger ms-auto">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
            
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 table-hover">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-50px rounded-start">No</th>
                            <th class="min-w-150px">Tgl Transaksi</th>
                            <th class="min-w-200px">Nama Member</th>
                            <th class="min-w-150px">Layanan/Paket</th>
                            <th class="min-w-100px text-center">Status</th>
                            <th class="min-w-150px text-end pe-4 rounded-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="ps-4">
                                <span class="text-dark fw-bold fs-6">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="text-gray-800 fs-7 fw-bold">{{ $transaction->created_at->format('d M Y') }}</div>
                                <div class="text-muted fs-8">{{ $transaction->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bold text-hover-primary fs-6">{{ $transaction->user->name ?? 'Member Umum' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-info fs-7 px-3 py-2">
                                    <i class="fas fa-dumbbell me-1 text-info fs-8"></i> {{ $transaction->service->name ?? 'Layanan' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($transaction->status == 'completed')
                                    <span class="badge badge-light-success px-3 py-2"><i class="fas fa-check-circle me-1 text-success fs-8"></i>Selesai</span>
                                @elseif($transaction->status == 'scheduled')
                                    <span class="badge badge-light-primary px-3 py-2"><i class="fas fa-calendar-check me-1 text-primary fs-8"></i>Dijadwalkan</span>
                                @else
                                    <span class="badge badge-light-warning px-3 py-2"><i class="fas fa-clock me-1 text-warning fs-8"></i>{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <span class="text-success fw-bold d-block fs-5">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <i class="fas fa-file-invoice-dollar fs-3x text-muted mb-4 opacity-50"></i>
                                <div class="fs-5 text-gray-600 mb-2">Belum Ada Transaksi Layanan</div>
                                <div class="text-muted fs-7">Anda belum memiliki pendapatan/transaksi pada bulan ini.</div>
                            </td>
                        </tr>
                        @endforelse
                        
                        @if($transactions->count() > 0)
                        <tr class="bg-light-success">
                            <td colspan="5" class="text-end fw-bold fs-5 py-4">TOTAL PENDAPATAN BULAN INI:</td>
                            <td class="text-end pe-4 py-4 fw-bolder text-success fs-3">
                                Rp {{ number_format($totalSalary, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 text-muted fs-7 text-center">
                <i class="fas fa-info-circle me-1"></i> Data yang ditampilkan adalah rincian layanan member yang telah disetujui/dibeli.
            </div>
        </div>
    </div>
</div>
@endsection
