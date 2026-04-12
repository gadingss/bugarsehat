@extends('layouts.app-login')

@section('content')
<div class="w-lg-500px p-10">
    <!--begin::Form-->
    <form class="form w-100" method="POST" action="{{ route('password.otp.submit') }}">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">
        
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Two-Step Verification</h1>
            <!--end::Title-->
            <!--begin::Link-->
            <div class="text-muted fw-semibold fs-5 mb-5">Enter the verification code we sent via WhatsApp to</div>
            <!--end::Link-->
            <!--begin::Mobile no-->
            <div class="fw-bold text-dark fs-3">{{ $phone }}</div>
            <!--end::Mobile no-->
        </div>
        <!--end::Heading-->

        @if (session('status'))
            <div class="alert alert-success text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!--begin::Input group-->
        <div class="mb-10">
            <!--begin::Label-->
            <div class="fw-bold text-center text-dark fs-6 mb-5">Type your 6 digit security code</div>
            <!--end::Label-->
            
            <div class="d-flex flex-center gap-3">
                <input type="text" name="otp" maxlength="6" class="form-control bg-transparent text-center fw-bold fs-1 @error('otp') is-invalid @enderror" placeholder="000000" style="padding: 15px; letter-spacing: 12px;" required autofocus />
            </div>
            @error('otp')
                <div class="text-danger text-center mt-2">{{ $message }}</div>
            @enderror
        </div>
        <!--end::Input group-->

        <!--begin::Submit-->
        <div class="d-grid mb-10">
            <button type="submit" class="btn btn-primary">
                <span class="indicator-label">Verify OTP</span>
            </button>
        </div>
        <!--end::Submit-->

        <!--begin::Notice-->
        <div class="text-center text-gray-500 fw-semibold fs-6">
            Didn't get the code?
            <a href="javascript:document.getElementById('resend-form').submit();" class="link-primary fw-bold">Resend</a>
        </div>
        <!--end::Notice-->
    </form>
    
    <form id="resend-form" method="POST" action="{{ route('password.otp.send') }}" style="display: none;">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">
    </form>
</div>
@endsection
