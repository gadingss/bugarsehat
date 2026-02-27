@extends('layouts.app')

@section('title', 'Validasi Pembelian Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="fw-bold">Validasi Pembelian Produk</h2>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-3">
                            <select class="form-select form-select-solid w-200px" id="status-filter">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu Validasi</option>
                                <option value="validated">Divalidasi</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Member</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
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
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($transaction->status == 'pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($transaction->status == 'validated')
                                            <span class="badge badge-success">Divalidasi</span>
                                        @else
                                            <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-success validate-btn" data-id="{{ $transaction->id }}">
                                                <i class="fas fa-check me-1"></i>Validasi
                                            </button>
                                            <button class="btn btn-sm btn-danger reject-btn" data-id="{{ $transaction->id }}">
                                                <i class="fas fa-times me-1"></i>Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-gray-500">Tidak ada transaksi untuk divalidasi</div>
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

<!-- Modal Konfirmasi -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="validation-message">Apakah Anda yakin ingin <span id="action-type"></span> transaksi ini?</p>
                <textarea id="validation-note" class="form-control" placeholder="Catatan (opsional)" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirm-validation">Ya</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentTransactionId = null;
    let currentAction = null;

    // Validation buttons
    document.querySelectorAll('.validate-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentTransactionId = this.dataset.id;
            currentAction = 'validate';
            document.getElementById('action-type').textContent = 'mengvalidasi';
            new bootstrap.Modal(document.getElementById('validationModal')).show();
        });
    });

    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentTransactionId = this.dataset.id;
            currentAction = 'reject';
            document.getElementById('action-type').textContent = 'menolak';
            new bootstrap.Modal(document.getElementById('validationModal')).show();
        });
    });

    document.getElementById('confirm-validation').addEventListener('click', function() {
        const note = document.getElementById('validation-note').value;
        
        fetch(`/staff/transactions/${currentTransactionId}/${currentAction}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ note: note })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        });
    });
});
</script>
@endsection
