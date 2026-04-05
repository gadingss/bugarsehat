<div class="row">
    <div class="col-md-5">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                class="img-fluid rounded shadow-sm w-100" style="max-height: 260px; object-fit: cover;"
                id="modal-product-img-{{ $product->id }}">
        @else
            <div class="bg-light-primary rounded d-flex align-items-center justify-content-center" style="height: 260px;"
                id="modal-product-img-{{ $product->id }}">
                <div class="text-center text-gray-400">
                    <i class="ki-duotone ki-picture fs-3x mb-2"><span class="path1"></span><span class="path2"></span></i>
                    <p class="mb-0 fs-7">Belum ada gambar</p>
                </div>
            </div>
        @endif

        @if(Auth::user()->hasRole('User:Owner') || Auth::user()->hasRole('User:Staff'))
            <div class="card border-dashed border-primary bg-light-primary mt-3">
                <div class="card-body p-3">
                    <p class="fw-bold text-primary mb-2 fs-7">
                        <i class="ki-duotone ki-picture fs-6 me-1"><span class="path1"></span><span
                                class="path2"></span></i>
                        {{ $product->image ? 'Ganti Gambar' : 'Upload Gambar' }}
                    </p>
                    <form action="{{ route('products.update-image', $product->id) }}" method="POST"
                        enctype="multipart/form-data" id="upload-product-form-{{ $product->id }}"
                        onsubmit="submitProductImage(event, {{ $product->id }})">
                        @csrf
                        <div class="mb-2">
                            <input type="file" name="image" accept="image/*" class="form-control form-control-sm"
                                onchange="previewModalProductImage(this, {{ $product->id }})">
                            <small class="text-muted">JPG, PNG, WEBP. Maks 3MB.</small>
                        </div>
                        <div id="modal-product-preview-{{ $product->id }}" class="mb-2 d-none">
                            <img src="" id="modal-product-preview-img-{{ $product->id }}" class="img-fluid rounded border"
                                style="max-height: 100px; object-fit: cover;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="ki-duotone ki-cloud-upload fs-5 me-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                            Simpan Gambar
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-7 d-flex flex-column">
        <div>
            <span class="badge badge-light-primary fs-7 mb-3">{{ $product->category }}</span>
            <p class="text-gray-600 fs-6 mb-3">{{ $product->description }}</p>

            <div class="mb-3">
                <span class="text-gray-600">Stok: </span>
                <span class="fw-bold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $product->stock }} unit
                </span>
            </div>

            <div class="mb-4">
                @if($product->is_promo && $product->promo_price)
                    <div class="d-flex align-items-center mb-1">
                        <span class="fs-1 fw-bold text-primary me-3">Rp
                            {{ number_format($product->promo_price, 0, ',', '.') }}</span>
                        <span class="fs-5 text-gray-500 text-decoration-line-through">Rp
                            {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-success fw-bold fs-7">Hemat Rp
                        {{ number_format($product->price - $product->promo_price, 0, ',', '.') }}</div>
                @else
                    <span class="fs-1 fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
            </div>
        </div>

        <div class="mt-auto">
            @if($product->stock > 0)
                <button class="btn btn-primary w-100"
                    onclick="addToCart({{ $product->id }}); bootstrap.Modal.getInstance(document.getElementById('productDetailModal')).hide();">
                    <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                </button>
            @else
                <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
            @endif
        </div>
    </div>
</div>