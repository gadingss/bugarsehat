@extends('layouts.app')

@section('title', $config['title'] ?? 'Kelola Membership')

@section('content')
<div class="app-content flex-column-fluid">
    <div class="app-container container-xxl">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="fas fa-search fs-3 position-absolute ms-5"></i>
                        <input type="text" id="searchTable" class="form-control form-control-solid w-250px ps-13" placeholder="Cari Paket..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_packet">
                        <i class="fas fa-plus"></i> Tambah Paket
                    </button>
                </div>
            </div>
            
            <div class="card-body py-4">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                        <i class="fas fa-check-circle fs-2hx text-success me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-success">Berhasil!</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                        <i class="fas fa-exclamation-triangle fs-2hx text-danger me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-danger">Gagal!</h4>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="packet_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Nama Paket</th>
                                <th class="min-w-125px">Harga</th>
                                <th class="min-w-100px">Durasi (Hari)</th>
                                <th class="min-w-125px">Maks Kunjungan</th>
                                <th class="min-w-200px">Deskripsi</th>
                                <th class="text-end min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($packets as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                            <div class="symbol-label bg-light-primary text-primary fs-3 fw-bolder">
                                                {{ substr($p->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 text-hover-primary mb-1 fw-bold fs-5">{{ $p->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($p->price == 0)
                                        <span class="badge badge-light-success fw-bold px-3 py-2">GRATIS</span>
                                    @else
                                        Rp {{ number_format($p->price, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>{{ $p->duration_days }}</td>
                                <td>
                                    @if($p->max_visits == 999)
                                        <span class="badge badge-light-info">Unlimited</span>
                                    @else
                                        {{ $p->max_visits }} kali
                                    @endif
                                </td>
                                <td>{{ Str::limit($p->description, 50) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('master_membership.edit', $p->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deletePacket({{ $p->id }}, '{{ addslashes($p->name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-muted">Belum ada data paket membership.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Paket -->
<div class="modal fade" id="kt_modal_add_packet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ isset($packet) ? 'Edit' : 'Tambah' }} Paket Membership</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-2"></i>
                </div>
            </div>
            <form action="{{ isset($packet) ? route('packet_membership.update', $packet->id ?? '') : route('packet_membership.store') }}" method="POST">
                @csrf
                @if(isset($packet)) @method('PUT') @endif
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Nama Paket</label>
                        <input type="text" name="name" class="form-control form-control-solid" value="{{ old('name', $packet->name ?? '') }}" required placeholder="Contoh: Gold Membership" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control form-control-solid" value="{{ old('price', $packet->price ?? '') }}" required min="0" />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-semibold fs-6 mb-2">Durasi (Hari)</label>
                            <input type="number" name="duration_days" class="form-control form-control-solid" value="{{ old('duration_days', $packet->duration_days ?? '') }}" required min="1" placeholder="30" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-semibold fs-6 mb-2">Maks Kunjungan</label>
                            <input type="number" name="max_visits" class="form-control form-control-solid" value="{{ old('max_visits', $packet->max_visits ?? '') }}" required min="1" placeholder="Isi 999 untuk Unlimited" />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Layanan yang Didapat (Bundle)</label>
                        <select name="services[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Layanan" data-allow-clear="true" multiple>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" 
                                    @if(isset($packet) && $packet->services->contains($svc->id)) selected @endif>
                                    {{ $svc->name }} ({{ $svc->sessions_count }} sesi)
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-7 mt-2">Member akan otomatis mendapatkan kuota layanan ini ketika member sudah aktif.</div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Detail Produk Fisik</label>
                        <textarea name="product_details" class="form-control form-control-solid" rows="2" placeholder="Contoh: Gratis Handuk dan Botol Minum (Member dapat klaim ke Staff)">{{ old('product_details', $packet->product_details ?? '') }}</textarea>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Deskripsi Membership</label>
                        <textarea name="description" class="form-control form-control-solid" rows="3">{{ old('description', $packet->description ?? '') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        var table = $('#packet_table').DataTable({
            "info": false,
            "order": [],
            "pageLength": 10,
            "lengthChange": false,
        });

        $('#searchTable').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Auto open modal on edit redirect
        @if(isset($packet))
            $('#kt_modal_add_packet').modal('show');
            $('#kt_modal_add_packet').on('hidden.bs.modal', function () {
                window.location.href = "{{ route('master_membership') }}";
            });
        @endif
    });

    function deletePacket(id, name) {
        Swal.fire({
            html: `Anda yakin ingin menghapus paket <strong>${name}</strong>?`,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function (result) {
            if (result.value) {
                const form = document.getElementById('delete-form');
                form.action = "{{ route('packet_membership.destroy', '') }}/" + id;
                form.submit();
            }
        });
    }
</script>
@endsection
