@extends('layouts.app')

@section('title', 'Checkout Paket Membership')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h2 class="fw-bold">Checkout Paket Membership</h2>
                </div>
                <div class="card-toolbar">
                    {{-- Menggunakan route yang sudah diperbaiki --}}
                    <a href="{{ route('packet_membership') }}" class="btn btn-light">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Kembali
                    </a>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="row">
                    <!--begin::Package Details-->
                    <div class="col-lg-8">
                        <!--begin::Package Info-->
                        <div class="card mb-8">
                            <div class="card-header">
                                <h3 class="card-title">Detail Paket</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-8">
                                    <div class="symbol symbol-60px me-5">
                                        <span class="symbol-label bg-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} text-inverse-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }}">
                                            <span class="fs-1 fw-bold">{{ substr($packet->name, 0, 1) }}</span>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold mb-1">{{ $packet->name }}</h3>
                                        <div class="fs-2 fw-bold text-primary">
                                            @if($packet->price == 0)
                                                <span class="text-success">GRATIS</span>
                                            @else
                                                Rp {{ number_format($packet->price, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <p class="text-gray-600 mb-4">{{ $packet->description }}</p>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/></svg>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold">Durasi Paket</div>
                                                    <div class="text-gray-600">{{ $packet->duration_days }} hari</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/></svg>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold">Maksimal Kunjungan</div>
                                                    <div class="text-gray-600">{{ $packet->max_visits == 999 ? 'Unlimited' : $packet->max_visits . ' kali' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Package Info-->

                        @if($activeMembership)
                        <!--begin::Active Membership Warning-->
                        <div class="alert alert-warning d-flex align-items-center p-5 mb-8">
                            <span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                                    <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-warning">Anda sudah memiliki membership aktif</h4>
                                <span>Membership {{ $activeMembership->package->name }} Anda masih aktif hingga {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d M Y') }}. Pembelian paket baru akan menggantikan membership yang ada.</span>
                            </div>
                        </div>
                        <!--end::Active Membership Warning-->
                        @endif

                        <!--begin::Payment Form-->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $packet->price == 0 ? 'Konfirmasi Aktivasi' : 'Metode Pembayaran' }}</h3>
                            </div>
                            <div class="card-body">
                                {{-- Form action diisi dengan route yang benar --}}
                                <form action="{{ route('packet_membership.purchase', $packet->id) }}" method="POST" id="checkout-form">
                                    @csrf
                                    
                                    @if($packet->price > 0)
                                    <div class="mb-8">
                                        <label class="required fw-semibold fs-6 mb-5">Pilih Metode Pembayaran</label>
                                        
                                        <div class="row g-5">
                                            <!--begin::QRIS Option-->
                                            <div class="col-6 col-lg-3">
                                                <input type="radio" class="btn-check" name="payment_method" value="qris" id="payment_qris" required checked>
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="payment_qris">
                                                    <span class="svg-icon svg-icon-3x me-5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 5H9V9H5V5Z" fill="currentColor"/><path d="M15 5H19V9H15V5Z" fill="currentColor"/><path d="M5 15H9V19H5V15Z" fill="currentColor"/><path d="M16 16V14H18V11H20V14H22V16H20V18H18V21H16V18H14V16H16Z" fill="currentColor"/><path opacity="0.3" d="M3 3H11V11H3V3Z" fill="currentColor"/><path opacity="0.3" d="M13 3H21V11H13V3Z" fill="currentColor"/><path opacity="0.3" d="M3 13H11V21H3V13Z" fill="currentColor"/><path opacity="0.3" d="M13 13H21V21H13V13Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                    <span class="d-block fw-semibold text-start">
                                                        <span class="text-dark fw-bold d-block fs-4 mb-2">QRIS</span>
                                                        <span class="text-muted fw-semibold fs-6">Scan QR</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <!--end::QRIS Option-->

                                            <!--begin::Cash Option-->
                                            <div class="col-6 col-lg-3">
                                                <input type="radio" class="btn-check" name="payment_method" value="cash" id="payment_cash">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="payment_cash">
                                                    <span class="svg-icon svg-icon-3x me-5">
                                                        {{-- Icon untuk Cash --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70001 10.3C7.30001 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="currentColor"/>
                                                            <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C10.7 16.1 11.3 16.1 11.7 15.7Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                    <span class="d-block fw-semibold text-start">
                                                        <span class="text-dark fw-bold d-block fs-4 mb-2">Cash</span>
                                                        <span class="text-muted fw-semibold fs-6">Bayar di Tempat</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <!--end::Cash Option-->
                                            
                                            <!--begin::Transfer Option-->
                                            <div class="col-6 col-lg-3">
                                                <input type="radio" class="btn-check" name="payment_method" value="transfer" id="payment_transfer">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="payment_transfer">
                                                    <span class="svg-icon svg-icon-3x me-5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M8 11h8v2H8v-2zM8 7h12v2H8V7zM8 15h5v2H8v-2zM3 3h18c.55 0 1 .45 1 1v16c0 .55-.45 1-1 1H3c-.55 0-1-.45-1-1V4c0-.55.45-1 1-1zm0 2v14h18V5H3z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                    <span class="d-block fw-semibold text-start">
                                                        <span class="text-dark fw-bold d-block fs-4 mb-2">Transfer</span>
                                                        <span class="text-muted fw-semibold fs-6">Bank Transfer</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <!--end::Transfer Option-->

                                            <!--begin::Midtrans/Card Option-->
                                            <div class="col-6 col-lg-3">
                                                <input type="radio" class="btn-check" name="payment_method" value="midtrans" id="payment_midtrans">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="payment_midtrans">
                                                    <span class="svg-icon svg-icon-3x me-5">
                                                        <!-- credit card icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M2 10h20" stroke="currentColor" stroke-width="2"/></svg>
                                                    </span>
                                                    <span class="d-block fw-semibold text-start">
                                                        <span class="text-dark fw-bold d-block fs-4 mb-2">Midtrans</span>
                                                        <span class="text-muted fw-semibold fs-6">Kartu / Online</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <!--end::Midtrans/Card Option-->
                                        </div>
                                        @error('payment_method')
                                            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @else
                                    <input type="hidden" name="payment_method" value="free">
                                    <div class="alert alert-success d-flex align-items-center p-5 mb-8">
                                        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-1 text-success">Paket Gratis!</h4>
                                            <span>Paket {{ $packet->name }} akan langsung diaktifkan setelah konfirmasi.</span>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-8">
                                        <label class="fw-semibold fs-6 mb-2">Catatan (Opsional)</label>
                                        <textarea name="notes" class="form-control form-control-solid" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="text-danger fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{ route('packet_membership') }}" class="btn btn-light">Batal</a>
                                        <button type="submit" class="btn btn-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }}" id="submit-btn">
                                            <span class="indicator-label">
                                                {{ $packet->price == 0 ? 'Aktifkan Paket' : 'Proses Pembelian' }}
                                            </span>
                                            <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--end::Payment Form-->
                    </div>
                    <!--end::Package Details-->
                    
                    <!--begin::Order Summary-->
                    <div class="col-lg-4">
                        <div class="card position-sticky" style="top: 100px;">
                            <div class="card-header">
                                <h3 class="card-title">Ringkasan Pesanan</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-gray-600">Paket:</span>
                                    <span class="fw-bold">{{ $packet->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-gray-600">Durasi:</span>
                                    <span class="fw-bold">{{ $packet->duration_days }} hari</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold text-gray-600">Kunjungan:</span>
                                    <span class="fw-bold">{{ $packet->max_visits == 999 ? 'Unlimited' : $packet->max_visits . ' kali' }}</span>
                                </div>
                                <div class="separator my-5"></div>
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <span class="fs-3 fw-bold text-gray-800">Total:</span>
                                    <span class="fs-2 fw-bold text-primary">
                                        @if($packet->price == 0)
                                            <span class="text-success">GRATIS</span>
                                        @else
                                            Rp {{ number_format($packet->price, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="bg-light-primary rounded p-5">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/></svg>
                                        </span>
                                        <span class="fw-bold text-primary">Benefit yang Anda dapatkan:</span>
                                    </div>
                                    <ul class="list-unstyled text-gray-700 fw-semibold">
                                        <li class="d-flex align-items-center py-1"><span class="bullet bullet-dot bg-primary me-3"></span>Akses semua peralatan gym</li>
                                        <li class="d-flex align-items-center py-1"><span class="bullet bullet-dot bg-primary me-3"></span>Konsultasi gratis dengan trainer</li>
                                        <li class="d-flex align-items-center py-1"><span class="bullet bullet-dot bg-primary me-3"></span>Akses locker room</li>
                                        <li class="d-flex align-items-center py-1"><span class="bullet bullet-dot bg-primary me-3"></span>Program latihan personal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Order Summary-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>

<!--begin::Modal - Payment Details-->
<div class="modal fade" id="payment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal-title">Detail Pembayaran</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7" id="modal-body">
                {{-- Konten modal akan diisi oleh JavaScript --}}
            </div>
             <div class="modal-footer">
                {{-- Menggunakan route yang sudah diperbaiki --}}
                <a href="{{ route('transaction.history') }}" class="btn btn-primary">Lihat Riwayat Transaksi</a>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->
@endsection

@section('script')
{{-- include midtrans snap js; client key fetched from config --}}
@if(config('midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
{{-- SweetAlert2 & jQuery biasanya sudah ada di layout utama (app.blade.php) --}}
<script>
$(document).ready(function() {
    const submitBtn = $('#submit-btn');
    const form = $('#checkout-form');
    const paymentModal = new bootstrap.Modal(document.getElementById('payment-modal'));
    
    form.on('submit', function(e) {
        e.preventDefault(); // Mencegah form submit cara biasa

        submitBtn.attr('data-kt-indicator', 'on');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'), // Mengambil URL dari atribut 'action' form
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Handle redirect untuk paket gratis atau pembayaran cash
                    if (response.redirect_url) {
                        let message = response.message || "Proses berhasil!";
                        
                        Swal.fire({
                            text: message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, Lanjutkan!",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = response.redirect_url;
                            }
                        });
                        return; // Hentikan eksekusi lebih lanjut
                    }

                    // Handle modal untuk QRIS dan Transfer
                    let modalTitle = 'Instruksi Pembayaran';
                    let modalContent = '';

                    switch(response.payment_method) {
                        case 'qris':
                            modalTitle = 'Pindai untuk Membayar';
                            modalContent = `
                                <div class="text-center">
                                    <p class="fs-4 text-gray-700 mb-4">${response.message}</p>
                                    <img src="${response.qr_code_url}" class="img-fluid" alt="QR Code" style="max-width: 250px;"/>
                                    <div class="mt-5">
                                        <p class="fw-bold fs-5">Invoice ID: ${response.invoice_id}</p>
                                        <p class="fw-bold fs-3">Total: Rp ${new Intl.NumberFormat('id-ID').format(response.total)}</p>
                                    </div>
                                </div>
                            `;
                            break;

                        case 'transfer':
                            modalTitle = 'Transfer Bank';
                            modalContent = `
                                <div class="mb-5">
                                    <p class="fs-4 text-gray-700 mb-4">${response.message}</p>
                                    <div class="py-5 px-7 rounded-3" style="background-color: #f1faff;">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-gray-600">Nama Bank:</span>
                                            <span class="fw-bold text-dark">${response.bank_details.bank_name}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-gray-600">Nomor Rekening:</span>
                                            <span class="fw-bold text-dark">${response.bank_details.account_number}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-gray-600">Atas Nama:</span>
                                            <span class="fw-bold text-dark">${response.bank_details.account_name}</span>
                                        </div>
                                    </div>
                                    <div class="mt-5 text-center">
                                        <p class="fw-bold fs-5">Invoice ID: ${response.invoice_id}</p>
                                        <p class="fw-bold fs-3">Total Transfer: <span class="text-primary">Rp ${new Intl.NumberFormat('id-ID').format(response.total)}</span></p>
                                        <p class="fs-7 text-danger">*Pastikan nominal transfer sesuai hingga digit terakhir.</p>
                                    </div>
                                </div>
                            `;
                            break;

                        case 'midtrans':
                            // midtrans snap popup
                            if (response.snap_token) {
                                snap.pay(response.snap_token, {
                                    onSuccess: function(result) {
                                        Swal.fire({
                                            text: 'Pembayaran berhasil, menunggu konfirmasi.',
                                            icon: 'success',
                                            buttonsStyling: false,
                                            confirmButtonText: 'Ok',
                                            customClass: { confirmButton: 'btn btn-primary' }
                                        }).then(() => {
                                            window.location.href = '{{ route('transaction.history') }}';
                                        });
                                    },
                                    onPending: function(result) {
                                        Swal.fire({
                                            text: 'Transaksi masih pending. Silakan cek histori transaksi.',
                                            icon: 'info',
                                            buttonsStyling: false,
                                            confirmButtonText: 'Tutup',
                                            customClass: { confirmButton: 'btn btn-primary' }
                                        }).then(() => {
                                            window.location.href = '{{ route('transaction.history') }}';
                                        });
                                    },
                                    onError: function(result) {
                                        Swal.fire({
                                            text: 'Pembayaran gagal: ' + result.status_message,
                                            icon: 'error',
                                            buttonsStyling: false,
                                            confirmButtonText: 'Tutup',
                                            customClass: { confirmButton: 'btn btn-danger' }
                                        });
                                    },
                                    onClose: function() {
                                        Swal.fire({
                                            text: 'Anda menutup popup pembayaran.',
                                            icon: 'warning',
                                            buttonsStyling: false,
                                            confirmButtonText: 'Ok',
                                            customClass: { confirmButton: 'btn btn-secondary' }
                                        });
                                    }
                                });
                            }
                            return; // skip showing modal
                    }
                    
                    $('#modal-title').text(modalTitle);
                    $('#modal-body').html(modalContent);
                    paymentModal.show();
                }
            },
            error: function(xhr) {
                let errorMessage = "Terjadi kesalahan. Silakan coba lagi.";
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                Swal.fire({
                    text: errorMessage,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Tutup",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            },
            complete: function() {
                submitBtn.removeAttr('data-kt-indicator');
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
