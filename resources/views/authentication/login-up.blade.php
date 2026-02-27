@extends('layouts.app-login')
@section('content')
<div class="w-lg-500px p-10">
    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" method="POST" action="{{ route('register.post') }}">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-11">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">
                Sign Up
            </h1>
            <!--end::Title-->
        </div>
        <!--begin::Heading-->

        <!--begin::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Name-->
            <input type="text" placeholder="Name" name="name" autocomplete="off" class="form-control bg-transparent" required autofocus value="{{ old('name') }}"/>
            <!--end::Name-->
            <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" required value="{{ old('email') }}"/>
            <!--end::Email-->
            <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
        <!--end::Input group--->
        <div class="fv-row mb-8" data-kt-password-meter="true">
            <!--begin::Wrapper-->
            <div class="mb-1">
                <!--begin::Input wrapper-->
                <div class="position-relative mb-3">
                    <input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off" required/>
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                        <i class="ki-duotone ki-eye-slash fs-2"></i>
                        <i class="ki-duotone ki-eye fs-2 d-none"></i>
                    </span>
                </div>
                <!--end::Input wrapper-->
                <div class="fv-plugins-message-container invalid-feedback"></div>
                <!--begin::Meter-->
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div>
                <!--end::Meter-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Hint-->
            <div class="text-muted">
                Use 8 or more characters with a mix of letters, numbers & symbols.
            </div>
            <!--end::Hint-->
        </div>
        <!--end::Input group--->
        <!--end::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Repeat Password-->
            <input placeholder="Repeat Password" name="password_confirmation" type="password" autocomplete="off" class="form-control bg-transparent" required/>
            <!--end::Repeat Password-->
            <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
        <!--end::Input group--->

        <!-- role selector (not shown in original UI) -->
        {{--
            If you want to allow creating a trainer from the registration page,
            uncomment the block below and adjust permissions accordingly.  By default
            every new user is given the "User:Member" role in AuthController.
            Most deployments should create trainers via the admin panel or tinker
            rather than expose this widget publicly.
        --}}
        {{--
        <div class="fv-row mb-8">
            <label for="role" class="form-label">Account Type</label>
            <select name="role" class="form-control bg-transparent">
                <option value="User:Member" selected>Member</option>
                <option value="User:Trainer">Trainer</option>
            </select>
        </div>
        --}}


        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                <!--begin::Indicator label-->
                <span class="indicator-label">
                    Sign up</span>
                <!--end::Indicator label-->
                <!--begin::Indicator progress-->
                <span class="indicator-progress">
                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
                <!--end::Indicator progress-->
            </button>
        </div>
        <!--end::Submit button-->
        <!--begin::Sign up-->
        <div class="text-gray-500 text-center fw-semibold fs-6">
            Already have an Account?
            <a href="{{ route('login') }}" class="link-primary fw-semibold">
                Sign in
            </a>
        </div>
        <!--end::Sign up-->
    </form>
    <!--end::Form-->
</div>
@endsection
@section('script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('kt_sign_up_form');
    const submitButton = document.getElementById('kt_sign_up_submit');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(function(el) {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(function(el) {
            el.innerHTML = '';
        });

        // Show indicator and disable button
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // On success, redirect
                window.location.href = data.data.direct;
            } else {
                 // Handle generic error message
                if (data.message && !data.data) {
                    Swal.fire({
                        text: data.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
                // Handle validation errors
                if (data.data && typeof data.data === 'object') {
                    Object.keys(data.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorContainer = input.closest('.fv-row').querySelector('.invalid-feedback');
                            if (errorContainer) {
                                errorContainer.innerHTML = data.data[key].join('<br>');
                            }
                        }
                    });
                     // Show first error in a popup for better visibility
                    const firstErrorKey = Object.keys(data.data)[0];
                    const firstErrorMessage = data.data[firstErrorKey][0];
                     Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again. Error: " + firstErrorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                text: "An unexpected error occurred. Please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        })
        .finally(() => {
            // Hide indicator and enable button
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
        });
    });
});
</script>
@endsection