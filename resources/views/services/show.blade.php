@extends('layouts.app')

@section('title', $service->name)

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
        <div class="row g-5 g-xl-10">
            <div class="col-xl-8 mx-auto">
                <div class="card card-flush shadow-sm">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark fs-2 mb-1">{{ $service->name }}</span>
                            <span class="text-muted fw-semibold fs-7">{{ $service->category }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('services.index') }}" class="btn btn-sm btn-light-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-5">
                        @include('services.partials.detail')
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6">
                        @if($service->requires_booking)
                            <button class="btn btn-primary"
                                onclick="alert('Silakan gunakan tombol booking di halaman daftar layanan untuk saat ini.')">
                                <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                            </button>
                        @endif
                    </div>
                </div>

                @if($relatedServices->count() > 0)
                    <div class="mt-10">
                        <h3 class="fw-bold mb-5">Layanan Terkait</h3>
                        <div class="row g-5">
                            @foreach($relatedServices as $rel)
                                <div class="col-md-4">
                                    <div class="card card-flush h-100 shadow-sm">
                                        <div class="card-body p-5">
                                            <h5 class="fw-bold mb-2">{{ $rel->name }}</h5>
                                            <div class="text-primary fw-bold mb-3">Rp {{ number_format($rel->price, 0, ',', '.') }}
                                            </div>
                                            <a href="{{ route('services.show', $rel->id) }}"
                                                class="btn btn-sm btn-light-primary w-100">Lihat</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection