@extends('layouts.app')

@section('title', 'Penggajian Trainer')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="card card-flush shadow-sm">
        <div class="card-header bg-primary pt-7 pb-5">
            <h3 class="card-title align-items-start flex-column text-white">
                <span class="card-label fw-bold fs-3 mb-1"><i class="fas fa-money-check-alt me-2 text-white"></i>Penggajian Trainer</span>
                <span class="text-white opacity-75 mt-1 fw-semibold fs-7">Akumulasi pendapatan dari transaksi layanan member</span>
            </h3>
        </div>
        
        <div class="card-body py-5">
            <!-- Filter Section -->
            <form method="GET" action="{{ route('owner.trainer-salary.index') }}" class="mb-6 d-flex flex-wrap align-items-center gap-3 bg-light rounded p-4 border border-dashed border-gray-300">
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
                    <a href="{{ route('owner.trainer-salary.index') }}" class="btn btn-sm btn-light-danger ms-auto">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
            
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 table-hover">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-50px rounded-start">No</th>
                            <th class="min-w-200px">Nama Trainer</th>
                            <th class="min-w-150px text-center">Total Transaksi/Layanan</th>
                            <th class="min-w-200px text-end pe-4 rounded-end">Total Gaji Akumulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $trainerId => $salary)
                        <tr>
                            <td class="ps-4">
                                <span class="text-dark fw-bold fs-6">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        @if($salary['trainer'] && $salary['trainer']->avatar)
                                            <img src="{{ Storage::url($salary['trainer']->avatar) }}" alt="{{ $salary['trainer']->name }}"/>
                                        @else
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                {{ substr($salary['trainer']->name ?? '?', 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bold text-hover-primary fs-6">{{ $salary['trainer']->name ?? 'Trainer Tidak Ditemukan' }}</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Instruktur Layanan</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-info fs-6 px-4 py-2">
                                    <i class="fas fa-dumbbell me-1 text-info"></i> {{ $salary['total_sessions'] }} Layanan
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="text-success fw-bold fs-4 mb-2">Rp {{ number_format($salary['total_salary'], 0, ',', '.') }}</div>
                                <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $trainerId }}">
                                    <i class="fas fa-list-ul me-1"></i> Rincian Member
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-10">
                                <i class="fas fa-file-invoice-dollar fs-3x text-muted mb-4 opacity-50"></i>
                                <div class="fs-5 text-gray-600 mb-2">Belum Ada Transaksi Layanan</div>
                                <div class="text-muted fs-7">Belum ada pendapatan yang masuk dari transaksi layanan member.</div>
                            </td>
                        </tr>
                        @endforelse
                        
                        @if($salaries->count() > 0)
                        <tr class="bg-light-success">
                            <td colspan="3" class="text-end fw-bold fs-4 py-4">TOTAL KESELURUHAN PENDAPATAN SEMUA TRAINER:</td>
                            <td class="text-end pe-4 py-4 fw-bolder text-success fs-3">
                                Rp {{ number_format($salaries->sum('total_salary'), 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Details --}}
@foreach($salaries as $trainerId => $salary)
<div class="modal fade" id="modalDetail{{ $trainerId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="fas fa-user-tie text-primary me-2"></i> Rincian Pendapatan: {{ $salary['trainer']->name ?? 'Trainer' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle m-0 p-3">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-5 min-w-100px py-3">Tgl Transaksi</th>
                                <th class="min-w-150px">Nama Member</th>
                                <th class="min-w-150px">Layanan/Paket</th>
                                <th>Status</th>
                                <th class="text-end pe-5">Nominal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salary['details'] as $detail)
                            <tr>
                                <td class="ps-5">
                                    <div class="text-gray-800 fs-7">{{ $detail->created_at->format('d M Y') }}</div>
                                    <div class="text-muted fs-8">{{ $detail->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold fs-6">{{ $detail->user->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-dark fs-7">{{ $detail->service->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($detail->status == 'completed')
                                        <span class="badge badge-light-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                    @elseif($detail->status == 'scheduled')
                                        <span class="badge badge-light-primary"><i class="fas fa-calendar-check me-1"></i>Dijadwalkan</span>
                                    @else
                                        <span class="badge badge-light-warning"><i class="fas fa-clock me-1"></i>{{ ucfirst($detail->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-success fw-bold pe-5">
                                    Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light p-4 d-flex justify-content-between align-items-center w-100">
                <div class="d-flex flex-column text-start">
                    <span class="text-muted fs-7">Jumlah Transaksi Masuk</span>
                    <span class="fw-bold fs-5">{{ $salary['total_sessions'] }} Kali Transaksi</span>
                </div>
                <div class="d-flex flex-column text-end">
                    <span class="text-muted fs-7">Total Hak Gaji/Pendapatan</span>
                    <span class="fw-bolder fs-2 text-success">Rp {{ number_format($salary['total_salary'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
