@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* ── Summary Cards ── */
    .income-stat-card {
        border-radius: 0.75rem !important;
        border: none !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        overflow: hidden !important;
        background: #fff !important;
    }
    .income-stat-card:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1) !important;
    }
    .income-stat-icon {
        width: 52px !important;
        height: 52px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.2rem !important;
        flex-shrink: 0 !important;
    }
    .income-stat-icon.icon-today { background: rgba(37, 99, 235, 0.1) !important; color: #2563eb !important; }
    .income-stat-icon.icon-month { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
    .income-stat-icon.icon-trx { background: rgba(139, 92, 246, 0.1) !important; color: #8b5cf6 !important; }
    .income-stat-icon.icon-avg { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }

    .income-stat-label {
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #9ca3af !important;
        margin-bottom: 4px !important;
    }
    .income-stat-value {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1f2937 !important;
        line-height: 1.2 !important;
    }

    /* ── Main Report Card ── */
    .income-report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 1.25rem 1.5rem !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
    .income-report-header h4,
    .income-report-header .text-white {
        color: #fff !important;
    }

    /* ── Export Buttons ── */
    .btn-exp-excel {
        background: #10b981 !important;
        border: none !important;
        color: #fff !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        font-size: 0.82rem !important;
    }
    .btn-exp-excel:hover { background: #059669 !important; color: #fff !important; }
    .btn-exp-pdf {
        background: #ef4444 !important;
        border: none !important;
        color: #fff !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        font-size: 0.82rem !important;
    }
    .btn-exp-pdf:hover { background: #dc2626 !important; color: #fff !important; }

    /* ── Filter Card ── */
    .income-filter-card {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        background: #fafbfc !important;
        box-shadow: none !important;
    }

    /* ── Table ── */
    .income-tbl thead th {
        background: #f8f9fa !important;
        border-bottom: 2px solid #e5e7eb !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #6b7280 !important;
        padding: 12px 16px !important;
    }
    .income-tbl tbody td {
        padding: 12px 16px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f3f4f6 !important;
        font-size: 0.88rem !important;
        color: #374151 !important;
    }
    .income-tbl tbody tr:hover {
        background-color: #f0f4ff !important;
    }

    /* ── Category Badges ── */
    .income-badge {
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        padding: 4px 10px !important;
        border-radius: 50px !important;
        display: inline-block !important;
        letter-spacing: 0.3px !important;
    }
    .income-badge-produk { background: rgba(37, 99, 235, 0.12) !important; color: #2563eb !important; }
    .income-badge-layanan { background: rgba(16, 185, 129, 0.12) !important; color: #059669 !important; }
    .income-badge-lainnya { background: rgba(107, 114, 128, 0.12) !important; color: #6b7280 !important; }

    /* ── Total Footer ── */
    .income-total-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem 1.5rem !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35) !important;
    }
    .income-total-bar * {
        color: #fff !important;
    }
    .income-total-label {
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        opacity: 0.9 !important;
    }
    .income-total-value {
        font-size: 1.6rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.5px !important;
    }
    .income-total-sub {
        font-size: 0.78rem !important;
        opacity: 0.7 !important;
    }

    /* ── Period Info ── */
    .income-period-info {
        font-size: 0.82rem !important;
        color: #9ca3af !important;
    }
    .income-period-info strong {
        color: #6b7280 !important;
    }

    /* ── Empty State ── */
    .income-empty {
        padding: 3rem 1rem !important;
        text-align: center !important;
    }
    .income-empty i {
        font-size: 3rem !important;
        color: #d1d5db !important;
        margin-bottom: 1rem !important;
    }
    .income-empty p {
        color: #9ca3af !important;
        font-size: 0.95rem !important;
    }
</style>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        {{-- ═══ Summary Cards ═══ --}}
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="income-stat-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="income-stat-icon icon-today">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div>
                            <div class="income-stat-label">Pendapatan Hari Ini</div>
                            <div class="income-stat-value">Rp {{ number_format($incomeToday ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="income-stat-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="income-stat-icon icon-month">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="income-stat-label">Pendapatan Bulan Ini</div>
                            <div class="income-stat-value">Rp {{ number_format($incomeThisMonth ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="income-stat-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="income-stat-icon icon-trx">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <div class="income-stat-label">Total Transaksi (Periode)</div>
                            <div class="income-stat-value">{{ number_format($totalTransactions ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="income-stat-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="income-stat-icon icon-avg">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <div class="income-stat-label">Rata-rata per Transaksi</div>
                            <div class="income-stat-value">Rp {{ number_format($averageTransaction ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Main Report Card ═══ --}}
        <div class="card" style="border: none; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden;">
            {{-- Header --}}
            <div class="income-report-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="mb-0 text-white" style="font-weight: 700;">
                    <i class="fas fa-chart-line me-2"></i>Laporan Pemasukan
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-exp-excel">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-exp-pdf">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body p-6">
                {{-- Filter Section --}}
                <div class="income-filter-card p-4 mb-5">
                    <form method="GET" action="{{ route('income_report') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-semibold" style="font-size: 0.85rem; color: #6b7280;">
                                <i class="fas fa-calendar me-1"></i>Dari Tanggal
                            </label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-semibold" style="font-size: 0.85rem; color: #6b7280;">
                                <i class="fas fa-calendar me-1"></i>Sampai Tanggal
                            </label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">
                                <i class="fas fa-search me-1"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Period Info --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <span class="income-period-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan data: <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YYYY') }}</strong>
                        &mdash;
                        <strong>{{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YYYY') }}</strong>
                    </span>
                    <span class="income-period-info">
                        {{ $totalTransactions ?? 0 }} transaksi ditemukan
                    </span>
                </div>

                {{-- Income Table --}}
                <div class="table-responsive">
                    <table class="table income-tbl mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th style="width: 120px;">Kategori</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @forelse($incomes as $income)
                                <tr>
                                    <td style="color: #9ca3af;">{{ $counter++ }}</td>
                                    <td>
                                        <span style="font-weight: 600; color: #1f2937;">{{ \Carbon\Carbon::parse($income->created_at)->isoFormat('D MMM YYYY') }}</span>
                                        <br>
                                        <small style="color: #9ca3af;">{{ \Carbon\Carbon::parse($income->created_at)->isoFormat('dddd') }}</small>
                                    </td>
                                    <td>{{ $income->description }}</td>
                                    <td>
                                        @php
                                            $type = $income->type ?? 'lainnya';
                                            $badgeClass = match($type) {
                                                'produk' => 'income-badge-produk',
                                                'layanan' => 'income-badge-layanan',
                                                default => 'income-badge-lainnya',
                                            };
                                            $badgeLabel = match($type) {
                                                'produk' => 'Produk',
                                                'layanan' => 'Layanan',
                                                default => 'Lainnya',
                                            };
                                        @endphp
                                        <span class="income-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>
                                    <td class="text-end" style="font-weight: 600; color: #1f2937;">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="income-empty">
                                            <i class="fas fa-inbox d-block"></i>
                                            <p class="mb-0">Tidak ada data pemasukan untuk periode yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Total Footer --}}
                @if(($totalTransactions ?? 0) > 0)
                <div class="mt-5">
                    <div class="income-total-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="income-total-label">Total Pemasukan Periode Ini</div>
                            <div class="income-total-sub">
                                {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YYYY') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YYYY') }}
                            </div>
                        </div>
                        <div class="income-total-value">
                            Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
