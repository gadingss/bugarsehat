@extends('layouts.app')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="card mb-7">
                    <div class="card-header">
                        <div class="card-title">
                            <h3><i class="fas fa-filter text-primary me-3"></i>Opsi Filter</h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="collapse"
                                data-bs-target="#kt_transaction_filter">
                                Sembunyikan/Tampilkan Filter
                            </a>
                        </div>
                    </div>
                    <div class="collapse show" id="kt_transaction_filter">
                        <form method="GET" action="{{ route('transaction_report') }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-5">
                                        <label class="form-label fs-6 fw-bold">Status:</label>
                                        <select class="form-select form-select-solid" name="status" data-kt-select2="true"
                                            data-placeholder="Pilih Status" data-hide-search="true">
                                            <option value="">Semua Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validated</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-5">
                                        <label class="form-label fs-6 fw-bold">Produk:</label>
                                        <select class="form-select form-select-solid" name="product_id"
                                            data-kt-select2="true" data-placeholder="Pilih Produk">
                                            <option value="">Semua Produk</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-5">
                                        <label class="form-label fs-6 fw-bold">Tanggal Dari:</label>
                                        <input type="date" class="form-control form-control-solid" name="date_from"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3 mb-5">
                                        <label class="form-label fs-6 fw-bold">Tanggal Sampai:</label>
                                        <input type="date" class="form-control form-control-solid" name="date_to"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end py-6">
                                <a href="{{ route('transaction_report') }}" class="btn btn-light me-3">Reset</a>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Laporan Transaksi</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Kelola dan pantau semua transaksi</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-end">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none">
                                            <path
                                                d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    Export
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4"
                                    data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="{{ route('transaction_report.export', array_merge(request()->all(), ['export_type' => 'excel'])) }}"
                                            class="menu-link px-3">
                                            <i class="fas fa-file-excel text-success me-3"></i> Excel
                                        </a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="{{ route('transaction_report.export', array_merge(request()->all(), ['export_type' => 'pdf'])) }}"
                                            class="menu-link px-3">
                                            <i class="fas fa-file-pdf text-danger me-3"></i> PDF
                                        </a>
                                    </div>
                                </div>
                                @can('create', \App\Models\Transaction::class)
                                    <a href="{{ route('transaction_report.create') }}" class="btn btn-primary">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                                    transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                            </svg>
                                        </span>
                                        Tambah Transaksi
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-5 g-xl-8 mb-8">
                            <div class="col-lg-3 col-md-6">
                                <div
                                    class="card card-flush bgi-position-y-center bgi-position-x-end bgi-no-repeat bgi-size-contain bg-light-primary h-100 hoverable">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="text-primary fs-2x fw-bolder">
                                                    {{ number_format($summary['total_transactions']) }}</div>
                                                <div class="text-muted fw-semibold fs-6">Total Transaksi</div>
                                            </div>
                                            <div class="ms-4"><i class="fas fa-exchange-alt text-primary fs-3x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div
                                    class="card card-flush bgi-position-y-center bgi-position-x-end bgi-no-repeat bgi-size-contain bg-light-success h-100 hoverable">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="text-success fs-2x fw-bolder">Rp
                                                    {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
                                                <div class="text-muted fw-semibold fs-6">Total Pendapatan</div>
                                            </div>
                                            <div class="ms-4"><i class="fas fa-money-bill-wave text-success fs-3x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div
                                    class="card card-flush bgi-position-y-center bgi-position-x-end bgi-no-repeat bgi-size-contain bg-light-warning h-100 hoverable">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="text-warning fs-2x fw-bolder">
                                                    {{ number_format($summary['pending_count']) }}</div>
                                                <div class="text-muted fw-semibold fs-6">Pending</div>
                                            </div>
                                            <div class="ms-4"><i class="fas fa-clock text-warning fs-3x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div
                                    class="card card-flush bgi-position-y-center bgi-position-x-end bgi-no-repeat bgi-size-contain bg-light-info h-100 hoverable">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="text-info fs-2x fw-bolder">
                                                    {{ number_format($summary['validated_count']) }}</div>
                                                <div class="text-muted fw-semibold fs-6">Validated</div>
                                            </div>
                                            <div class="ms-4"><i class="fas fa-check-circle text-info fs-3x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Member</th>
                                        <th class="min-w-125px">Produk</th>
                                        <th class="min-w-125px">Tanggal Transaksi</th>
                                        <th class="min-w-100px">Total</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-125px">Validator</th>
                                        <th class="text-end min-w-100px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                        {{ substr($transaction->user->name, 0, 1) }}</div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $transaction->user->name }}</span>
                                                    <span class="text-muted">{{ $transaction->user->email }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $transaction->item->name ?? '-' }}</td>
                                            <td>{{ $transaction->transaction_date->format('d M Y, H:i') }}</td>
                                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($transaction->status == 'pending')
                                                    <span class="badge badge-light-warning">Pending</span>
                                                @elseif($transaction->status == 'validated')
                                                    <span class="badge badge-light-success">Validated</span>
                                                @elseif($transaction->status == 'cancelled')
                                                    <span class="badge badge-light-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->validator ? $transaction->validator->name : '-' }}</td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Aksi
                                                    <span class="svg-icon svg-icon-5 m-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                </a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('transaction_report.show', $transaction) }}"
                                                            class="menu-link px-3">Lihat Detail</a>
                                                    </div>
                                                    @can('update', $transaction)
                                                        <div class="menu-item px-3">
                                                            <a href="{{ route('transaction_report.edit', $transaction) }}"
                                                                class="menu-link px-3">Edit</a>
                                                        </div>
                                                    @endcan
                                                    @if($transaction->status == 'pending' && auth()->user()->can('validate', $transaction))
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3"
                                                                onclick="event.preventDefault(); document.getElementById('validate-form-{{ $transaction->id }}').submit();">Validasi</a>
                                                            <form id="validate-form-{{ $transaction->id }}"
                                                                action="{{ route('transaction_report.validate', $transaction) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                            </form>
                                                        </div>
                                                    @endif
                                                    @if($transaction->status != 'cancelled' && auth()->user()->can('cancel', $transaction))
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 text-danger"
                                                                onclick="if(confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')) { event.preventDefault(); document.getElementById('cancel-form-{{ $transaction->id }}').submit(); }">Batalkan</a>
                                                            <form id="cancel-form-{{ $transaction->id }}"
                                                                action="{{ route('transaction_report.cancel', $transaction) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="d-flex flex-column align-items-center py-10">
                                                    <h4 class="text-gray-600">Tidak Ada Data Transaksi</h4>
                                                    <p class="text-muted">Silakan ubah filter Anda atau tambahkan transaksi
                                                        baru.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <div class="text-muted fs-7">
                                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari
                                {{ $transactions->total() }} data
                            </div>
                            <div>
                                {{ $transactions->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Script tambahan jika diperlukan, misalnya untuk re-initialize Select2 setelah AJAX
        // Untuk saat ini, fungsionalitas dasar sudah ditangani oleh atribut data-kt-*
    </script>
@endsection