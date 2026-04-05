@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="card shadow-sm">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <span class="svg-icon svg-icon-1 position-absolute ms-4"><i class="fas fa-search"></i></span>
                    <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Cari Layanan..." />
                </div>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_service">
                    <i class="fas fa-plus"></i> Tambah Layanan
                </button>
            </div>
        </div>

        <div class="card-body pt-0">
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_services">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">No</th>
                        <th class="min-w-150px">Layanan</th>
                        <th class="min-w-100px">Kategori</th>
                        <th class="min-w-100px text-end">Harga</th>
                        <th class="min-w-100px text-end">Sesi / Durasi</th>
                        <th class="min-w-70px text-center">Status</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @foreach($services as $service)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url('{{ $service->image ? asset('storage/' . $service->image) : asset('metronic/assets/media/stock/600x400/img-1.jpg') }}');"></span>
                                </a>
                                <div class="ms-5">
                                    <span class="text-gray-800 fw-bold fs-5">{{ $service->name }}</span>
                                    <div class="text-muted fs-7">{{ Str::limit($service->description, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light-info">{{ $service->category }}</span>
                        </td>
                        <td class="text-end text-dark fw-bold text-success fs-5">
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            <span class="badge badge-light-primary mb-1">{{ $service->sessions_count }} Sesi</span><br>
                            <span class="fs-8 text-muted"><i class="fas fa-clock text-warning"></i> {{ $service->getFormattedDuration() }}</span>
                        </td>
                        <td class="text-center">
                            @if($service->is_active)
                                <span class="badge badge-light-success">Aktif</span>
                            @else
                                <span class="badge badge-light-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-light-warning btn-sm me-1" 
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_service_{{ $service->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('service.destroy', $service->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-light-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="kt_modal_edit_service_{{ $service->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit Layanan</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="fas fa-times fs-1"></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form class="form" action="{{ route('service.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Nama Layanan</label>
                                            <input type="text" class="form-control form-control-solid" name="name" value="{{ $service->name }}" required />
                                        </div>

                                        <div class="row g-9 mb-7">
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Harga (Rp)</label>
                                                <input type="number" class="form-control form-control-solid" name="price" value="{{ $service->price }}" required />
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Kategori</label>
                                                <input type="text" class="form-control form-control-solid" name="category" value="{{ $service->category }}" required />
                                            </div>
                                        </div>

                                        <div class="row g-9 mb-7">
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Jumlah Sesi</label>
                                                <input type="number" class="form-control form-control-solid" name="sessions_count" value="{{ $service->sessions_count }}" required min="1"/>
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Durasi per Sesi (Menit)</label>
                                                <input type="number" class="form-control form-control-solid" name="duration_minutes" value="{{ $service->duration_minutes }}" required min="1"/>
                                            </div>
                                        </div>

                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold mb-2">Maks. Partisipan (Kosong = Unlimited)</label>
                                            <input type="number" class="form-control form-control-solid" name="max_participants" value="{{ $service->max_participants }}" />
                                        </div>

                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold mb-2">Deskripsi Layanan</label>
                                            <textarea class="form-control form-control-solid" name="description" rows="3">{{ $service->description }}</textarea>
                                        </div>

                                        <div class="row border p-4 rounded bg-light mb-7">
                                            <div class="col-md-6 fv-row">
                                                <div class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} id="status_edit_srv_{{ $service->id }}"/>
                                                    <label class="form-check-label fw-semibold text-gray-700 ms-3" for="status_edit_srv_{{ $service->id }}">Layanan Aktif</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <div class="form-check form-switch form-check-custom form-check-solid mt-4 mt-md-0">
                                                    <input class="form-check-input" type="checkbox" name="requires_booking" value="1" {{ $service->requires_booking ? 'checked' : '' }} id="req_book_{{ $service->id }}"/>
                                                    <label class="form-check-label fw-semibold text-gray-700 ms-3" for="req_book_{{ $service->id }}">Wajib Booking Sesi</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="fv-row mb-15">
                                            <div class="text-muted fs-7">Ubah gambar melalui Katalog / Detail produk/layanan. Atur informasi dasar di sini.</div>
                                        </div>

                                        <div class="text-center pt-15">
                                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="indicator-label">Simpan Perubahan</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="kt_modal_add_service" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Layanan Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form class="form" action="{{ route('service.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="fv-row mb-7">
                        <label class="required d-block fw-semibold fs-6 mb-5">Gambar Layanan</label>
                        <input type="file" name="image" class="form-control" accept=".png, .jpg, .jpeg" required/>
                        <div class="form-text">Maksimal ukuran file 2MB. Format: png, jpg, jpeg.</div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Layanan / Paket</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Misal: Paket PT 12 Sesi" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Harga (Rp)</label>
                            <input type="number" class="form-control form-control-solid" name="price" placeholder="Misal: 1500000" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Kategori</label>
                            <input type="text" class="form-control form-control-solid" name="category" placeholder="Misal: Personal Training" required />
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Jumlah Sesi</label>
                            <input type="number" class="form-control form-control-solid" name="sessions_count" value="1" required min="1"/>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Durasi per Sesi (Menit)</label>
                            <input type="number" class="form-control form-control-solid" name="duration_minutes" value="60" required min="1"/>
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Maks. Partisipan (Kosong = Unlimited)</label>
                        <input type="number" class="form-control form-control-solid" name="max_participants" value="" placeholder="Contoh: 1" />
                        <div class="form-text">Isi 1 untuk Private PT. Kosongkan untuk kelas publik.</div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Deskripsi Layanan</label>
                        <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="Informasi mengenai layanan..."></textarea>
                    </div>

                    <div class="row border p-4 rounded bg-light mb-7">
                        <div class="col-md-6 fv-row">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="status_add_srv" checked/>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="status_add_srv">Layanan Aktif</label>
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-4 mt-md-0">
                                <input class="form-check-input" type="checkbox" name="requires_booking" value="1" id="req_book_add" checked/>
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="req_book_add">Wajib Booking Sesi</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan Layanan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var table = $('#kt_datatable_services').DataTable({
            "info": false,
            'order': [],
            'pageLength': 10,
        });

        document.querySelector('[data-kt-filter="search"]').addEventListener('keyup', function(e) {
            table.search(e.target.value).draw();
        });
    });
</script>
@endsection
