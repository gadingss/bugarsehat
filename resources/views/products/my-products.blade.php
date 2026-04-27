@extends('layouts.app')
@section('title', 'Produk Saya')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <!-- Header -->
    <div class="row g-5 g-xl-6 mb-5">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="fw-bold"><i class="fas fa-box-open text-primary me-2"></i> Produk Saya</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle text-success fs-2 me-3"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-circle text-danger fs-2 me-3"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Produk dari Membership -->
    <div class="row g-5 g-xl-6 mb-8">
        <div class="col-12">
            <h3 class="fw-bold mb-4">Produk Benefit Membership</h3>
            @if($activeMembership && $membershipProducts->count() > 0)
                <div class="row g-4">
                    @foreach($membershipProducts as $mp)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card card-flush h-100 border border-primary border-dashed">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-60px rounded me-4 border">
                                            <img src="{{ $mp->product->image ? asset('storage/' . $mp->product->image) : asset('metronic/assets/media/stock/600x400/img-1.jpg') }}" alt="{{ $mp->product->name }}" onerror="this.src='{{ asset('metronic/assets/media/stock/600x400/img-1.jpg') }}'" style="object-fit: cover;" />
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-4">{{ $mp->product->name }}</span>
                                            <span class="badge badge-light-primary w-fit-content mt-1">Sisa Kuota: {{ $mp->remaining_quantity }}</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 fs-7 mb-4">{{ Str::limit($mp->product->description, 80) }}</p>
                                    @if($mp->remaining_quantity > 0)
                                    <form action="{{ route('products.use', $mp->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Apakah Anda yakin ingin menggunakan 1 kuota produk ini?')"><i class="fas fa-hand-holding-box me-2"></i> Gunakan Produk</button>
                                    </form>
                                    @else
                                        <button class="btn btn-light-secondary w-100" disabled>Kuota Habis</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card bg-light card-flush">
                    <div class="card-body text-center py-10">
                        <i class="fas fa-box-open fs-3x text-gray-400 mb-3"></i>
                        <div class="text-gray-600 fs-5">Anda tidak memiliki produk dari membership aktif saat ini.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Produk yang Dibeli -->
    <div class="row g-5 g-xl-6">
        <div class="col-12">
            <h3 class="fw-bold mb-4">Riwayat Pembelian Produk</h3>
            @if($purchasedProducts->count() > 0)
                <div class="row g-4">
                    @foreach($purchasedProducts as $transaction)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card card-flush h-100 border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-60px rounded me-4 border">
                                            <img src="{{ $transaction->product->image ? asset('storage/' . $transaction->product->image) : asset('metronic/assets/media/stock/600x400/img-1.jpg') }}" alt="{{ $transaction->product->name }}" onerror="this.src='{{ asset('metronic/assets/media/stock/600x400/img-1.jpg') }}'" style="object-fit: cover;" />
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-gray-800 fw-bold fs-4">{{ $transaction->product->name }}</span>
                                            <span class="text-gray-500 fs-7 mt-1"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column gap-2 mb-5">
                                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <span class="text-gray-600 fs-7">Jumlah Pembelian:</span>
                                            <span class="text-gray-800 fs-6 fw-bold">{{ $transaction->quantity }} Item</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <span class="text-gray-600 fs-7">Total Harga:</span>
                                            <span class="text-primary fs-6 fw-bold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <span class="badge badge-success fs-7 py-2 px-4"><i class="fas fa-check-circle text-white me-1"></i> Telah Divalidasi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($purchasedProducts->hasPages())
                    <div class="d-flex justify-content-center mt-6">
                        {{ $purchasedProducts->links() }}
                    </div>
                @endif
            @else
                <div class="card bg-light card-flush">
                    <div class="card-body text-center py-10">
                        <i class="fas fa-shopping-bag fs-3x text-gray-400 mb-3"></i>
                        <div class="text-gray-600 fs-5 mb-4">Belum ada riwayat pembelian produk satuan.</div>
                        <a href="{{ route('products.index') }}" class="btn btn-primary"><i class="fas fa-store me-2"></i> Lihat Katalog Produk</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
