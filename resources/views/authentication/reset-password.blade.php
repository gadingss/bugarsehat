@extends('layouts.app-login')

@section('content')
<div class="w-lg-500px p-10">
    <!--begin::Form-->
    <form class="form w-100" method="POST" action="{{ route('password.reset.submit') }}">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">
        
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Setup New Password</h1>
            <!--end::Title-->
            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">Already have a reset password?
            <a href="{{ route('login') }}" class="link-primary fw-bold">Sign in here</a></div>
            <!--end::Link-->
        </div>
        <!--begin::Heading-->

        @if (session('status'))
            <div class="alert alert-success">
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
        <div class="fv-row mb-8">
            <input type="password" placeholder="New Password" name="password" autocomplete="off" class="form-control bg-transparent @error('password') is-invalid @enderror" required />
            @error('password')
                <div class="invalid-feedback">{{ $error }}</div>
            @enderror
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-8">
            <input type="password" placeholder="Confirm Password" name="password_confirmation" autocomplete="off" class="form-control bg-transparent" required />
        </div>
        <!--end::Input group-->

        <!--begin::Submit-->
        <div class="d-grid mb-10">
            <button type="submit" class="btn btn-primary">
                <span class="indicator-label">Reset Password</span>
            </button>
        </div>
        <!--end::Submit-->
    </form>
    <!--end::Form-->
</div>
@endsection
