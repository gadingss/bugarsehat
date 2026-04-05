@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="card shadow-sm">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <span class="svg-icon svg-icon-1 position-absolute ms-4"><i class="fas fa-search"></i></span>
                    <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Cari Produk..." />
                </div>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_product">
                    <i class="fas fa-plus"></i> Tambah Produk
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

            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_products">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">No</th>
                        <th class="min-w-150px">Produk</th>
                        <th class="min-w-100px">Kategori</th>
                        <th class="min-w-100px text-end">Harga</th>
                        <th class="min-w-70px text-end">Stok</th>
                        <th class="min-w-70px text-center">Status</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url('{{ $product->image ? asset('storage/' . $product->image) : asset('metronic/assets/media/stock/600x400/img-1.jpg') }}');"></span>
                                </a>
                                <div class="ms-5">
                                    <span class="text-gray-800 fw-bold fs-5">{{ $product->name }}</span>
                                    <div class="text-muted fs-7">{{ Str::limit($product->description, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light-primary">{{ $product->category }}</span>
                        </td>
                        <td class="text-end">
                            @if($product->is_promo)
                                <div class="text-primary fw-bold">Rp {{ number_format($product->promo_price, 0, ',', '.') }}</div>
                                <div class="text-muted text-decoration-line-through fs-8">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            @else
                                <div class="text-dark fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="badge badge-light-{{ $product->stock > 0 ? 'success' : 'danger' }} fw-bold">{{ $product->stock }}</span>
                        </td>
                        <td class="text-center">
                            @if($product->is_active)
                                <span class="badge badge-light-success">Aktif</span>
                            @else
                                <span class="badge badge-light-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-light-warning btn-sm me-1" 
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_product_{{ $product->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-light-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="kt_modal_edit_product_{{ $product->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit Produk</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="fas fa-times fs-1"></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form class="form" action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Nama Produk</label>
                                            <input type="text" class="form-control form-control-solid" name="name" value="{{ $product->name }}" required />
                                        </div>

                                        <div class="row g-9 mb-7">
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Harga Reguler (Rp)</label>
                                                <input type="number" class="form-control form-control-solid" name="price" value="{{ $product->price }}" required />
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required fs-6 fw-semibold mb-2">Kategori</label>
                                                <input type="text" class="form-control form-control-solid" name="category" value="{{ $product->category }}" required />
                                            </div>
                                        </div>

                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Stok (Jumlah)</label>
                                            <input type="number" class="form-control form-control-solid" name="stock" value="{{ $product->stock }}" required />
                                        </div>

                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold mb-2">Deskripsi Produk</label>
                                            <textarea class="form-control form-control-solid" name="description" rows="3">{{ $product->description }}</textarea>
                                        </div>

                                        <div class="row g-9 mb-7">
                                            <div class="col-md-6 fv-row">
                                                <div class="form-check form-switch form-check-custom form-check-solid mt-6">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} id="status_edit_{{ $product->id }}"/>
                                                    <label class="form-check-label fw-semibold text-gray-400 ms-3" for="status_edit_{{ $product->id }}">Tampilkan di Katalog</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <div class="form-check form-switch form-check-custom form-check-solid mt-6">
                                                    <input class="form-check-input" type="checkbox" name="is_promo" value="1" {{ $product->is_promo ? 'checked' : '' }} id="promo_edit_{{ $product->id }}"/>
                                                    <label class="form-check-label fw-semibold text-danger ms-3" for="promo_edit_{{ $product->id }}">Sedang Promo</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold mb-2">Harga Promo (Rp) - <span class="text-muted">Isi jika sedang promo</span></label>
                                            <input type="number" class="form-control form-control-solid" name="promo_price" value="{{ $product->promo_price }}" />
                                        </div>
                                        
                                        <!-- NOTE: Image update intentionally left to the modal in the catalog, or we can add it here -->
                                        <!-- Since we already have updateImage logic, keeping it simple here OR adding standard file input: -->
                                        <div class="fv-row mb-15">
                                            <div class="text-muted fs-7">Ubah gambar melalui Katalog / Detail Produk. Atur informasi dasar di sini.</div>
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
<div class="modal fade" id="kt_modal_add_product" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Produk Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form class="form" action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="fv-row mb-7">
                        <label class="required d-block fw-semibold fs-6 mb-5">Gambar Produk</label>
                        <input type="file" name="image" class="form-control" accept=".png, .jpg, .jpeg" required/>
                        <div class="form-text">Maksimal ukuran file 2MB. Format: png, jpg, jpeg.</div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Produk</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Misal: Suplemen Whey" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Harga (Rp)</label>
                            <input type="number" class="form-control form-control-solid" name="price" placeholder="Misal: 150000" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Kategori</label>
                            <input type="text" class="form-control form-control-solid" name="category" placeholder="Misal: Suplemen" required />
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Stok Awal</label>
                        <input type="number" class="form-control form-control-solid" name="stock" value="0" required />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                        <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="Deskripsi mengenai produk..."></textarea>
                    </div>

                    <div class="fv-row mb-15">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="status_add" checked/>
                            <label class="form-check-label fw-semibold text-gray-400 ms-3" for="status_add">Langsung Aktifkan & Tampilkan di Katalog</label>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan Produk</span>
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
        var table = $('#kt_datatable_products').DataTable({
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
