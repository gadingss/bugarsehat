@extends('layouts.app')
@section('css')
@endsection
@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="row g-5 g-xl-6">
        <div class="col-12 col-lg-12">
            <!--begin::Engage widget 10-->
            <div class="card card-flush h-lg-100">
                <!--begin::Body-->
                <div class="card-body d-flex flex-column justify-content-between mt-2 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0">
                    <!--begin::Wrapper-->
                    <div class="mb-10">
                        <!--begin::Title-->
                        <div class="fs-2x fw-bold text-gray-800 text-center mb-6">
                            <span class="me-2">GYM & YOGA <br> ( Bugar Sehat - Gaya Hidup Sehat dan Aktif ) </span>
                        </div>
                        <!--end::Title-->
                    </div>
                    <!--begin::Wrapper-->
                    <!--begin::Illustration-->
                    <div class="text-center mb-4">
                        <img class="mx-auto h-150px h-lg-300px theme-light-show" src="{{ asset('metronic') }}/assets/media/illustrations/sketchy-1/9.png" alt="">
                        <img class="mx-auto h-150px h-lg-300px theme-dark-show" src="{{ asset('metronic') }}/assets/media/illustrations/sketchy-1/9-dark.png" alt="">
                    </div>
                    <!--end::Illustration-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Engage widget 10-->
        </div>
        
        <div class="col-12 col-lg-4">
            <!--begin::Chart widget 36-->
            <div class="card card-flush h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title align-items-start flex-column">
                        <h3 class="card-label fw-bold text-dark">Card Title 1</h3>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6" id="total">Label Card 1</span>
                    </div>
                    <!--end::Title-->
                    <div class="card-toolbar">
                        <select class="form-select form-control search-input form-control-sm form-select-sm rounded border" 
                            data-control="select2"
                            name="home-tgl"
                            id="home-tgl"  
                            data-placeholder="Pilih Tahun">
                                <option value="-">Semua Tahun</option>
                            @foreach($config['tahun'] as $tahun)
                                <option value="{{$tahun}}">{{$tahun}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0 h-lg-400px ">
                    <!--begin::Chart-->
                    <div id="chart_home" class="min-h-auto w-100 p-0 h-md-100" ></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->
        </div>
        <div class="col-12 col-lg-4">
            <!--begin::Chart widget 36-->
            <div class="card card-flush h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title align-items-start flex-column">
                        <h3 class="card-label fw-bold text-dark">Card Title 1</h3>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6" id="total">Label Card 1</span>
                    </div>
                    <!--end::Title-->
                    <div class="card-toolbar">
                        <select class="form-select form-control search-input form-control-sm form-select-sm rounded border" 
                            data-control="select2"
                            name="home-tgl"
                            id="home-tgl"  
                            data-placeholder="Pilih Tahun">
                                <option value="-">Semua Tahun</option>
                            @foreach($config['tahun'] as $tahun)
                                <option value="{{$tahun}}">{{$tahun}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0 h-lg-400px ">
                    <!--begin::Chart-->
                    <div id="chart_home" class="min-h-auto w-100 p-0 h-md-100" ></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->
        </div>
        <div class="col-12 col-lg-4">
            <!--begin::Chart widget 36-->
            <div class="card card-flush h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title align-items-start flex-column">
                        <h3 class="card-label fw-bold text-dark">Card Title 1</h3>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6" id="total">Label Card 1</span>
                    </div>
                    <!--end::Title-->
                    <div class="card-toolbar">
                        <select class="form-select form-control search-input form-control-sm form-select-sm rounded border" 
                            data-control="select2"
                            name="home-tgl"
                            id="home-tgl"  
                            data-placeholder="Pilih Tahun">
                                <option value="-">Semua Tahun</option>
                            @foreach($config['tahun'] as $tahun)
                                <option value="{{$tahun}}">{{$tahun}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0 h-lg-400px ">
                    <!--begin::Chart-->
                    <div id="chart_home" class="min-h-auto w-100 p-0 h-md-100" ></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->
        </div>
        <div class="col-12 col-lg-4">
            <!--begin::Chart widget 36-->
            <div class="card card-flush h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title align-items-start flex-column">
                        <h3 class="card-label fw-bold text-dark">Card Title 1</h3>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6" id="total">Label Card 1</span>
                    </div>
                    <!--end::Title-->
                    <div class="card-toolbar">
                        <select class="form-select form-control search-input form-control-sm form-select-sm rounded border" 
                            data-control="select2"
                            name="home-tgl"
                            id="home-tgl"  
                            data-placeholder="Pilih Tahun">
                                <option value="-">Semua Tahun</option>
                            @foreach($config['tahun'] as $tahun)
                                <option value="{{$tahun}}">{{$tahun}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0 h-lg-400px ">
                    <!--begin::Chart-->
                    <div id="chart_home" class="min-h-auto w-100 p-0 h-md-100" ></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->
        </div>
    </div>
</div>
@endsection
@section('script')
<script>

</script>
@endsection