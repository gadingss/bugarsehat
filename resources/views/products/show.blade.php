@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="fw-bold">{{ $product->name }}</h2>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('products.index') }}" class="btn btn-light-primary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bg-light-primary rounded" style="height: 400px; background-image: url('{{ $product->image ?? asset('metronic/assets/media/stock/600x400/img-1.jpg') }}'); background-size: cover; background-position: center;"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column h-100">
                                <div>
                                    <span class="badge badge-light-primary fs-7 mb-3">{{ $product->category }}</span>
                                    <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
                                    <p class="text-gray-600 fs-5 mb-4">{{ $product->description }}</p>
                                    
                                    <div class="mb-4">
                                        <span class="text-gray-600">Stok: </span>
                                        <span class="fw-bold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $product->stock }} unit
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        @if($product->is_promo && $product->promo_price)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="fs-1 fw-bold text-primary me-3">Rp {{ number_format($product->promo_price, 0, ',', '.') }}</span>
                                                <span class="fs-5 text-gray-500 text-decoration-line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-success fw-bold">
                                                Hemat Rp {{ number_format($product->price - $product->promo_price, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="fs-1 fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($product->stock > 0)
                                <div class="mt-auto">
                                    <form action="{{ route('products.purchase', $product->id) }}" method="POST" id="purchase-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="quantity" class="form-control form-control-lg" 
                                                   value="1" min="1" max="{{ $product->stock }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="mt-auto">
                                    <button class="btn btn-secondary btn-lg w-100" disabled>
                                        Stok Habis
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="fw-bold mb-4">Produk Terkait</h3>
            <div class="row g-5">
                @foreach($relatedProducts as $related)
                <div class="col-md-3">
                    <div class="card card-flush">
                        <div class="card-header p-0">
                            <div class="bg-light-primary rounded-top" style="height: 150px; background-image: url('{{ $related->image ?? asset('metronic/assets/media/stock/600x400/img-1.jpg') }}'); background-size: cover; background-position: center;"></div>
                        </div>
                        <div class="card-body">
                            <h5 class="fw-bold mb-2">{{ $related->name }}</h5>
                            <p class="text-gray-600 fs-7 mb-2">{{ Str::limit($related->description, 50) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">Rp {{ number_format($related->getCurrentPrice(), 0, ',', '.') }}</span>
                                <a href="{{ route('products.show', $related->id) }}" class="btn btn-sm btn-light-primary">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
document.getElementById('purchase-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const quantity = form.querySelector('input[name="quantity"]').value;
    
    Swal.fire({
        title: 'Konfirmasi Pembelian',
        text: `Anda akan membeli ${quantity} unit produk ini. Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Beli',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endsection
