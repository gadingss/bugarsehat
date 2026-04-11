@extends('layouts.app')

@section('title', 'Riwayat Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header pt-6">
                    <div class="card-title">
                        <h2 class="fw-bold">Riwayat Produk</h2>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" id="table_toolbar_base">
                            <button type="button" class="btn btn-light-primary" onclick="window.print()">
                                <i class="ki-duotone ki-printer fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                </i>
                                Cetak
                            </button>
                        </div>
                        <div class="d-flex justify-content-end align-items-center d-none" id="table_toolbar_selected">
                            <div class="fw-bold me-5">
                                <span class="me-2" id="selected_count">0</span> Terpilih
                            </div>
                            <button type="button" class="btn btn-danger" id="btn_bulk_delete">
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    @if(!auth()->user()->hasRole('User:Member') && auth()->user()->role !== 'member')
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target=".transaction-checkbox" value="1" />
                                        </div>
                                    </th>
                                    @endif
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Member</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    @if(!auth()->user()->hasRole('User:Member') && auth()->user()->role !== 'member')
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input transaction-checkbox" type="checkbox" value="{{ $transaction->id }}" />
                                        </div>
                                    </td>
                                    @endif
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px symbol-circle me-3">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="fas fa-box text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->product->name }}</div>
                                                <div class="text-muted fs-7">{{ $transaction->product->category }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px symbol-circle me-3">
                                                <div class="symbol-label bg-light-success">
                                                    <i class="fas fa-user text-success"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->user->name }}</div>
                                                <div class="text-muted fs-7">{{ $transaction->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $transaction->quantity }} unit</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @if($transaction->status == 'pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($transaction->status == 'validated')
                                            <span class="badge badge-success">Valid</span>
                                        @elseif($transaction->status == 'rejected')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-gray-500">Tidak ada riwayat transaksi produk yang ditemukan</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    const checkAll = document.querySelector('[data-kt-check="true"]');
    const toolbarBase = document.getElementById('table_toolbar_base');
    const toolbarSelected = document.getElementById('table_toolbar_selected');
    const selectedCount = document.getElementById('selected_count');
    const deleteBtn = document.getElementById('btn_bulk_delete');

    function toggleToolbars() {
        const checkedCount = document.querySelectorAll('.transaction-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedCount.innerHTML = checkedCount;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(c => {
                c.checked = checkAll.checked;
            });
            toggleToolbars();
        });
    }

    checkboxes.forEach(c => {
        c.addEventListener('change', function() {
            if(!this.checked && checkAll) checkAll.checked = false;
            toggleToolbars();
        });
    });

    if(deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.transaction-checkbox:checked')).map(c => c.value);
            
            Swal.fire({
                text: "Apakah Anda yakin ingin menghapus " + selectedIds.length + " transaksi terpilih?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route("product_transaction.bulk_delete") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, mengerti!",
                                    customClass: { confirmButton: "btn fw-bold btn-primary" }
                                }).then(function() {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: "Terjadi kesalahan saat menghapus data.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: { confirmButton: "btn fw-bold btn-primary" }
                            });
                        }
                    });
                }
            });
        });
    }
});
</script>
@endsection
