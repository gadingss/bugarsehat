@extends('layouts.app')

@section('title', 'Membership Berhasil Diaktifkan')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card body-->
            <div class="card-body py-20">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <!--begin::Success Icon-->
                        <div class="mb-10">
                            <span class="svg-icon svg-icon-5x svg-icon-success">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                                    <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                        <!--end::Success Icon-->

                        <!--begin::Title-->
                        <h1 class="fw-bolder fs-2qx text-gray-900 mb-4">Selamat!</h1>
                        <!--end::Title-->

                        <!--begin::Text-->
                        <div class="fw-semibold fs-6 text-gray-500 mb-7">
                            Membership <span class="fw-bold text-primary">{{ $membership->package->name }}</span> Anda telah berhasil diaktifkan
                        </div>
                        <!--end::Text-->

                        <!--begin::Membership Card-->
                        <div class="card bg-gradient-primary mb-10">
                            <div class="card-body text-center py-10">
                                <div class="d-flex justify-content-center mb-5">
                                    <div class="symbol symbol-80px">
                                        <span class="symbol-label bg-white text-primary">
                                            <span class="fs-2x fw-bold">{{ substr($membership->package->name, 0, 1) }}</span>
                                        </span>
                                    </div>
                                </div>
                                
                                <h2 class="fw-bold text-white mb-3">{{ $membership->package->name }} Membership</h2>
                                <div class="text-white opacity-75 mb-5">{{ $membership->package->description }}</div>
                                
                                <div class="row g-5 text-center">
                                    <div class="col-md-4">
                                        <div class="bg-white bg-opacity-20 rounded p-4">
                                            <div class="fs-2 fw-bold text-white mb-1">{{ $membership->package->duration_days }}</div>
                                            <div class="text-white opacity-75 fs-7">Hari</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white bg-opacity-20 rounded p-4">
                                            <div class="fs-2 fw-bold text-white mb-1">{{ $membership->remaining_visits == 999 ? '∞' : $membership->remaining_visits }}</div>
                                            <div class="text-white opacity-75 fs-7">Kunjungan</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white bg-opacity-20 rounded p-4">
                                            <div class="fs-2 fw-bold text-white mb-1">{{ \Carbon\Carbon::parse($membership->end_date)->diffInDays(\Carbon\Carbon::now()) }}</div>
                                            <div class="text-white opacity-75 fs-7">Hari Tersisa</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Membership Card-->

                        <!--begin::Details-->
                        <div class="card mb-10">
                            <div class="card-header">
                                <h3 class="card-title">Detail Membership</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-5">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-5">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Tanggal Mulai</div>
                                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($membership->start_date)->format('d F Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-5">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Tanggal Berakhir</div>
                                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($membership->end_date)->format('d F Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-5">
                                            <span class="svg-icon svg-icon-2 svg-icon-success me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Status</div>
                                                <div class="text-success fw-bold">{{ ucfirst($membership->status) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-5">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <div class="fw-semibold">Tipe</div>
                                                <div class="text-gray-600">{{ ucfirst($membership->type) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Details-->

                        <!--begin::Next Steps-->
                        <div class="card mb-10">
                            <div class="card-header">
                                <h3 class="card-title">Langkah Selanjutnya</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-5">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary mb-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.0173 9H15.3945C14.2833 9 13.263 9.61425 12.7431 10.5963L12.154 11.7091C12.0645 11.8781 12.1072 12.0868 12.2559 12.2071L12.6402 12.5183C13.2631 13.0225 13.7556 13.6691 14.0764 14.4035L14.2321 14.7601C14.2957 14.9058 14.4396 15 14.5987 15H18.6747C19.7297 15 20.4057 13.8774 19.912 12.945L18.6686 10.5963C18.1487 9.61425 17.1285 9 16.0173 9Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M14.0764 14.4035L14.2321 14.7601C14.2957 14.9058 14.4396 15 14.5987 15H18.6747C19.7297 15 20.4057 13.8774 19.912 12.945L18.6686 10.5963C18.1487 9.61425 17.1285 9 16.0173 9H15.3945C14.2833 9 13.263 9.61425 12.7431 10.5963L12.154 11.7091C12.0645 11.8781 12.1072 12.0868 12.2559 12.2071L12.6402 12.5183C13.2631 13.0225 13.7556 13.6691 14.0764 14.4035ZM8.07646 14.4035L8.23207 14.7601C8.29571 14.9058 8.43963 15 8.59872 15H4.32282C3.26777 15 2.59177 13.8774 3.08547 12.945L4.32887 10.5963C4.84878 9.61425 5.86901 9 6.98027 9H7.60303C8.71429 9 9.73451 9.61425 10.2544 10.5963L10.8435 11.7091C10.933 11.8781 10.8903 12.0868 10.7416 12.2071L10.3573 12.5183C9.73438 13.0225 9.24188 13.6691 8.92107 14.4035H8.07646Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <h5 class="fw-bold mb-2">Datang ke Gym</h5>
                                            <p class="text-gray-600 fs-7">Tunjukkan membership Anda kepada staff di front desk</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary mb-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <h5 class="fw-bold mb-2">Konsultasi Trainer</h5>
                                            <p class="text-gray-600 fs-7">Dapatkan program latihan yang sesuai dengan tujuan Anda</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary mb-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <h5 class="fw-bold mb-2">Mulai Berlatih</h5>
                                            <p class="text-gray-600 fs-7">Nikmati semua fasilitas gym dengan membership Anda</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Next Steps-->

                        <!--begin::Actions-->
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('packet_membership') }}" class="btn btn-light">Lihat Paket Lain</a>
                            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Dashboard</a>
                        </div>
                        <!--end::Actions-->

                        <!--begin::Contact Info-->
                        <div class="mt-10">
                            <div class="text-muted fs-7">
                                Butuh bantuan? Hubungi kami di <strong>+62 812-3456-7890</strong> atau email <strong>info@bugarsehat.com</strong>
                            </div>
                        </div>
                        <!--end::Contact Info-->
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
    // Auto redirect after 30 seconds
    setTimeout(function() {
        if (confirm('Halaman akan dialihkan ke dashboard. Klik OK untuk melanjutkan atau Cancel untuk tetap di halaman ini.')) {
            window.location.href = "{{ route('home') }}";
        }
    }, 30000);
</script>
@endsection
