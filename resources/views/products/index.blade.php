@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="fw-bold">Katalog Produk</h2>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <span class="svg-icon svg-icon-1 position-absolute ms-4 translate-middle-y top-50"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control form-control-solid w-250px ps-14" placeholder="Cari produk..." id="search-input"/>
                            </div>
                            <select class="form-select form-select-solid w-150px" id="category-filter">
                                <option value="">Semua Kategori</option>
                                <option value="Suplemen">Suplemen</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Yoga">Yoga</option>
                            </select>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="promo-filter">
                                <label class="form-check-label" for="promo-filter">Hanya Promo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-6" id="products-container">
        @forelse($products as $product)
        <div class="col-12 col-md-6 col-lg-4 product-item"
             data-category="{{ $product->category }}"
             data-promo="{{ $product->is_promo ? '1' : '0' }}"
             data-name="{{ strtolower($product->name) }}">
            <div class="card card-flush h-100">
                <div class="card-header p-0">
                    <div class="position-relative">
                        <div class="bg-light-primary rounded-top" style="height: 200px; background-image: url('{{ $product->image ? asset('storage/' . $product->image) : asset('metronic/assets/media/stock/600x400/img-1.jpg') }}'); background-size: cover; background-position: center;"></div>
                        @if($product->is_promo)
                        <div class="position-absolute top-0 end-0 m-3"><span class="badge badge-danger fs-7">PROMO</span></div>
                        @endif
                        @if($product->stock > 0 && $product->stock <= 5)
                        <div class="position-absolute top-0 start-0 m-3"><span class="badge badge-warning fs-7">Stok Terbatas</span></div>
                        @endif
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-2">{{ $product->name }}</h4>
                        <p class="text-gray-600 fs-7 mb-3">{{ Str::limit($product->description, 80) }}</p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-light-primary fs-8">{{ $product->category }}</span>
                            <span class="text-gray-500 ms-auto fs-8">Stok: {{ $product->stock }}</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        @if($product->is_promo && $product->promo_price)
                            <div class="d-flex align-items-center">
                                <span class="fs-2 fw-bold text-primary me-2">Rp {{ number_format($product->promo_price, 0, ',', '.') }}</span>
                                <span class="fs-6 text-gray-500 text-decoration-line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-success fs-8">Hemat Rp {{ number_format($product->price - $product->promo_price, 0, ',', '.') }}</div>
                        @else
                            <span class="fs-2 fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light-primary flex-fill" onclick="viewProduct({{ $product->id }})"><i class="fas fa-eye me-2"></i>Detail</button>
                        @if($product->stock > 0)
                            <button class="btn btn-primary flex-fill" onclick="addToCart({{ $product->id }})"><i class="fas fa-shopping-cart me-2"></i>Beli</button>
                        @else
                            <button class="btn btn-secondary flex-fill" disabled>Stok Habis</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-body text-center py-10">
                    <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak Ada Produk</div>
                    <div class="fs-6 text-gray-600">Belum ada produk yang tersedia saat ini</div>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="row mt-5">
        <div class="col-12 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal-product-name">Detail Produk</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="fas fa-times fs-1"></i></div>
            </div>
            <div class="modal-body" id="modal-product-content">
                </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Filter functionality (Tidak diubah)
document.addEventListener('DOMContentLoaded', function() {
    function filterProducts() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const selectedCategory = document.getElementById('category-filter').value;
        const promoOnly = document.getElementById('promo-filter').checked;
        const products = document.querySelectorAll('.product-item');
        
        products.forEach(product => {
            const productName = product.dataset.name;
            const productCategory = product.dataset.category;
            const isPromo = product.dataset.promo === '1';
            
            let show = true;
            if (searchTerm && !productName.includes(searchTerm)) show = false;
            if (selectedCategory && productCategory !== selectedCategory) show = false;
            if (promoOnly && !isPromo) show = false;
            
            product.style.display = show ? 'block' : 'none';
        });
    }

    document.getElementById('search-input').addEventListener('input', filterProducts);
    document.getElementById('category-filter').addEventListener('change', filterProducts);
    document.getElementById('promo-filter').addEventListener('change', filterProducts);
});


/**
 * [DIPERBAIKI] Fungsi untuk melihat detail produk
 */
function viewProduct(productId) {
    const modalElement = document.getElementById('productDetailModal');
    const modal = new bootstrap.Modal(modalElement);
    const modalContent = document.getElementById('modal-product-content');
    const modalTitle = document.getElementById('modal-product-name');

    // Tampilkan loading spinner
    modalTitle.textContent = 'Memuat...';
    modalContent.innerHTML = '<div class="text-center py-10"><div class="spinner-border text-primary" role="status"></div></div>';
    modal.show();
    
    // Ambil data dari server
    fetch(`{{ url('/products/show') }}/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalTitle.textContent = data.product.name;
                modalContent.innerHTML = data.html;
            } else {
                modalContent.innerHTML = `<div class="alert alert-danger">${data.message || 'Gagal memuat detail produk.'}</div>`;
            }
        })
        .catch(error => {
            console.error('View Product Error:', error);
            modalContent.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan. Tidak dapat terhubung ke server.</div>';
        });
}

/**
 * [DIPERBAIKI] Fungsi untuk membeli produk
 */
function addToCart(productId) {
    Swal.fire({
        title: 'Konfirmasi Pembelian',
        text: "Apakah Anda ingin membeli produk ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Beli",
        cancelButtonText: "Batal",
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membuat transaksi Anda.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`{{ url('/products/purchase') }}/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect ke halaman pembayaran/transaksi
                        window.location.href = data.redirect_url;
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                    icon: 'error'
                });
                console.error('Purchase Error:', error);
            });
        }
    });
}
</script>
@endsection