@extends('layouts.app')

@section('title', 'Pembayaran Membership')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h2 class="fw-bold">Pembayaran Membership</h2>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!--begin::Payment Info-->
                        <div class="card mb-8">
                            <div class="card-header">
                                <h3 class="card-title">Detail Pembayaran</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-8">
                                    <div class="symbol symbol-60px me-5">
                                        <span class="symbol-label bg-{{ $membership->package->name == 'Trial' ? 'info' : ($membership->package->name == 'Silver' ? 'secondary' : ($membership->package->name == 'Gold' ? 'warning' : ($membership->package->name == 'Platinum' ? 'primary' : 'success'))) }} text-inverse-{{ $membership->package->name == 'Trial' ? 'info' : ($membership->package->name == 'Silver' ? 'secondary' : ($membership->package->name == 'Gold' ? 'warning' : ($membership->package->name == 'Platinum' ? 'primary' : 'success'))) }}">
                                            <span class="fs-1 fw-bold">{{ substr($membership->package->name, 0, 1) }}</span>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold mb-1">{{ $membership->package->name }}</h3>
                                        <div class="fs-2 fw-bold text-primary">Rp {{ number_format($membership->package->price, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                
                                <div class="row g-5 mb-8">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16 2C16.5523 2 17 2.44772 17 3V5H20C21.1046 5 22 5.89543 22 7V19C22 20.1046 21.1046 21 20 21H4C2.89543 21 2 20.1046 2 19V7C2 5.89543 2.89543 5 4 5H7V3C7 2.44772 7.44772 2 8 2C8.55228 2 9 2.44772 9 3V5H15V3C15 2.44772 15.4477 2 16 2Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M22 7V19C22 20.1046 21.1046 21 20 21H4C2.89543 21 2 20.1046 2 19V7H22ZM7 10C6.44772 10 6 10.4477 6 11C6 11.5523 6.44772 12 7 12H17C17.5523 12 18 11.5523 18 11C18 10.4477 17.5523 10 17 10H7Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Periode Membership</div>
                                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($membership->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($membership->end_date)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Maksimal Kunjungan</div>
                                                <div class="text-gray-600">{{ $membership->remaining_visits == 999 ? 'Unlimited' : $membership->remaining_visits . ' kali' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Payment Info-->

                        <!--begin::Payment Instructions-->
                        <div class="card mb-8">
                            <div class="card-header">
                                <h3 class="card-title">Instruksi Pembayaran</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info d-flex align-items-center p-5 mb-8">
                                    <span class="svg-icon svg-icon-2hx svg-icon-info me-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="currentColor"/>
                                            <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-info">Status: Menunggu Pembayaran</h4>
                                        <span>Silakan lakukan pembayaran sesuai dengan metode yang dipilih, kemudian konfirmasi pembayaran kepada staff.</span>
                                    </div>
                                </div>

                                <div class="row g-5">
                                    <div class="col-md-6">
                                        <div class="bg-light-primary rounded p-5">
                                            <h5 class="fw-bold text-primary mb-3">Transfer Bank</h5>
                                            <div class="mb-3">
                                                <div class="fw-semibold">Bank BCA</div>
                                                <div class="text-gray-600">1234567890</div>
                                                <div class="text-gray-600">a.n. Bugar Sehat Gym</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="fw-semibold">Bank Mandiri</div>
                                                <div class="text-gray-600">0987654321</div>
                                                <div class="text-gray-600">a.n. Bugar Sehat Gym</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light-success rounded p-5">
                                            <h5 class="fw-bold text-success mb-3">Pembayaran Tunai</h5>
                                            <p class="text-gray-600 mb-3">Datang langsung ke gym dan lakukan pembayaran di front desk.</p>
                                            <div class="fw-semibold">Jam Operasional:</div>
                                            <div class="text-gray-600">Senin - Jumat: 06:00 - 22:00</div>
                                            <div class="text-gray-600">Sabtu - Minggu: 07:00 - 21:00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Payment Instructions-->

                        <!--begin::Actions-->
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="fw-bold mb-5">Sudah melakukan pembayaran?</h5>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('packet_membership') }}" class="btn btn-light">Kembali ke Paket</a>
                                    <button type="button" class="btn btn-success" onclick="confirmPayment({{ $membership->id }})">
                                        Konfirmasi Pembayaran
                                    </button>
                                </div>
                                
                                <div class="mt-8">
                                    <div class="text-muted fs-7">
                                        Butuh bantuan? Hubungi kami di <strong>+62 812-3456-7890</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Actions-->
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>
@endsection

@section('script')
<script>
    function confirmPayment(membershipId) {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Apakah Anda sudah melakukan pembayaran untuk membership ini?",
            icon: "question",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, sudah bayar",
            cancelButtonText: "Belum",
            customClass: {
                confirmButton: "btn fw-bold btn-success",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function (result) {
            if (result.value) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengaktifkan membership Anda',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                // Redirect to activation
                window.location.href = "{{ route('packet_membership.activate', '') }}/" + membershipId;
            }
        });
    }
</script>
@endsection
