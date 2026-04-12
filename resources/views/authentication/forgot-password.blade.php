@extends('layouts.app-login')

@section('content')
<div class="w-lg-500px p-10">
    <!--begin::Form-->
    <form class="form w-100" method="POST" action="{{ route('password.otp.send') }}">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Forgot Password ?</h1>
            <!--end::Title-->
            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">Enter your phone number to receive a 6-digit OTP code via WhatsApp to reset your password.</div>
            <!--end::Link-->
        </div>
        <!--begin::Heading-->

        <!--begin::Input group=-->
        <div class="fv-row mb-8">
            <!--begin::Phone-->
            <input type="text" placeholder="Phone Number (WhatsApp)" name="phone" autocomplete="off" class="form-control bg-transparent @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required />
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <!--end::Phone-->
            <div class="text-muted mt-2 fs-7">Example: 08123456789</div>
        </div>
        <!--begin::Actions-->
        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" class="btn btn-primary me-4">
                <span class="indicator-label">Send OTP</span>
            </button>
            <a href="{{ route('login') }}" class="btn btn-light">Cancel</a>
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Form-->
</div>
@endsection
