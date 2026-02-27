@extends('layouts.app-login')

@section('content')
<div class="w-lg-500px p-10">
    <!--begin::Form-->
    <form class="form w-100" method="post" novalidate="novalidate" id="kt_sign_in_form" action="{{$config['loginApi']}}">
        @csrf
        @method($data['method'])
        <!--begin::Heading-->
        <div class="text-center mb-8">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-5">Sign In</h1>
            <!--end::Title-->
            <!--begin::Subtitle-->
            <div class="fw-semibold fs-6">
                <span class="bg-secondary p-3 ">
                    <img alt="Logo" src="{{ asset('metronic/assets/media/misc/logo-only.png') }}" class="logo-in-login" />
                    Sign in with Credentials 
                </span>
                <span class="bg-primary text-white p-3">{{ config('app.name', 'Laravel') }}</span>
            </div>
            <!--end::Subtitle=-->
        </div>
        
        <!--begin::Input group=-->
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <input type="text" placeholder="Username/Email" name="email" autocomplete="off" class="form-control bg-transparent" />
            <!--end::Email-->
        </div>
        <!--end::Input group=-->
        <div class="fv-row mb-8 row-relative">
            <!--begin::Password-->
            <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
            <span class="pass-type" id="showHide">
                <i class="bi bi-eye text-primary icon-pass"></i>
            </span>
            <!--end::Password-->
        </div>
        <!--end::Input group=-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>
            <!--begin::Link-->
            <a href="../../demo1/dist/authentication/layouts/corporate/reset-password.html" class="link-primary">Forgot Password ?</a>
            <!--end::Link-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                <!--begin::Indicator label-->
                <span class="indicator-label">Sign In</span>
                <!--end::Indicator label-->
                <!--begin::Indicator progress-->
                <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                <!--end::Indicator progress-->
            </button>
        </div>
        <!--end::Submit button-->
        <!--begin::Sign up-->
        <div class="text-gray-500 text-center fw-semibold fs-6">Not a Member yet?
        <a href="{{route('register')}}" class="link-primary">Sign up</a></div>
        <!--end::Sign up-->
        <!--begin::Heading-->
        <!--begin::Separator-->
        <div class="separator separator-content my-14">
            <span class="w-125px text-gray-500 fw-semibold fs-7">Or with email</span>
        </div>
        <!--end::Separator-->
        <!--begin::Login options-->
        <div class="row g-3 mb-9">
            <!--begin::Col-->
            <div class="col-md-12">
                <!--begin::Google link=-->
                <a href="javascript:;" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                <img alt="Logo" src="{{ asset('metronic') }}/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Sign in with Google</a>
                <!--end::Google link=-->
            </div>
            <!--end::Col-->
            {{--
            <!--begin::Col-->
            <div class="col-md-6">
                <!--begin::Google link=-->
                <a href="javascript:;" readonly class="readonly btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                <img alt="Logo" src="{{ asset('metronic') }}/assets/media/svg/brand-logos/apple-black.svg" class="theme-light-show h-15px me-3" />
                <img alt="Logo" src="{{ asset('metronic') }}/assets/media/svg/brand-logos/apple-black-dark.svg" class="theme-dark-show h-15px me-3" />Sign in with Apple</a>
                <!--end::Google link=-->
            </div>
            <!--end::Col-->
            --}}
        </div>
        <!--end::Login options-->
        <!--end::Heading-->
        
    </form>
    <!--end::Form-->
</div>
@endsection
@section('script')
<script src="{{ asset('js') }}/authentication.js"></script>
@endsection