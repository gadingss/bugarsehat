@extends('layouts.app')

@section('title', 'Pilih Paket Membership')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 mb-5">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Pilih Paket Membership
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Paket Membership</li>
            </ul>
        </div>
        <!--end::Page title-->

        <!--begin::Alert for no packages-->
        @if($packets->isEmpty())
        <div class="card card-flush shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-10">
                <div class="symbol symbol-100px symbol-circle mx-auto mb-5">
                    <span class="symbol-label bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-exclamation-triangle fs-2x"></i>
                    </span>
                </div>
                <h2 class="fw-bold mb-3">Belum Ada Paket Tersedia</h2>
                <p class="text-gray-600 fs-5 mb-5">
                    Maaf, saat ini belum ada paket membership yang tersedia. 
                    Silakan hubungi staff kami untuk informasi lebih lanjut.
                </p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-home me-2"></i>Kembali ke Home
                </a>
            </div>
        </div>
        @else
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"/>
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <input type="text" data-kt-packet-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Cari Paket Membership"/>
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Packets Grid-->
                <div class="row g-6 g-xl-9">
                    @foreach($packets as $packet)
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <div class="card h-100 shadow-sm border-0 rounded-3">
                                <!--begin::Card header-->
                                <div class="card-header flex-nowrap border-0 pt-9 bg-light">
                                    <div class="card-title m-0">
                                        <div class="symbol symbol-45px w-45px bg-light me-5">
                                            <span class="symbol-label bg-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} text-inverse-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} rounded-circle">
                                                <span class="fs-2x fw-bold">{{ substr($packet->name, 0, 1) }}</span>
                                            </span>
                                        </div>
                                        <a href="#" class="fs-4 fw-bold text-hover-primary text-gray-600 m-0">{{ $packet->name }}</a>
                                    </div>
                                    @if($packet->price == 0)
                                        <span class="badge badge-success">GRATIS</span>
                                    @endif
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body d-flex flex-column px-9 pt-6 pb-8">
                                    <!--begin::Heading-->
                                    <div class="fs-2tx fw-bold mb-3 text-primary">
                                        @if($packet->price > 0)
                                            Rp {{ number_format($packet->price, 0, ',', '.') }}
                                        @else
                                            <span class="text-success">GRATIS</span>
                                        @endif
                                    </div>
                                    <!--end::Heading-->
                                    
                                    <!--begin::Description-->
                                    <div class="fs-7 fw-semibold text-gray-600 mb-7">
                                        {{ $packet->description }}
                                    </div>
                                    <!--end::Description-->
                                    
                                    <!--begin::Features-->
                                    <div class="d-flex flex-column text-gray-600 mb-7">
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            <span>Durasi: <strong>{{ $packet->duration_days }} hari</strong></span>
                                        </div>
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            <span>Kunjungan: <strong>{{ $packet->max_visits == 999 ? 'Unlimited' : $packet->max_visits . ' kali' }}</strong></span>
                                        </div>
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            <span>Akses: <strong>Semua fasilitas</strong></span>
                                        </div>
                                    </div>
                                    <!--end::Features-->
                                    
                                    <!--begin::Actions-->
                                    <div class="d-flex gap-2 mt-auto">
                                        <button type="button" class="btn btn-sm btn-light-primary flex-fill" onclick="showPacketDetail({{ $packet->id }})">
                                            <i class="fas fa-eye me-2"></i>Lihat Detail
                                        </button>
                                        <a href="{{ route('packet_membership.select', $packet->id) }}" class="btn btn-sm btn-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} flex-fill">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            {{ $packet->name == 'Trial' ? 'Ambil Trial' : 'Pilih Paket' }}
                                        </a>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                    @endforeach
                </div>
                <!--end::Packets Grid-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
        @endif
    </div>
</div>

<!--begin::Modal - Packet Detail-->
<div class="modal fade" id="kt_modal_packet_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="detail-modal-title">Detail Paket Membership</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="d-flex flex-column">
                    <!--begin::Header-->
                    <div class="d-flex align-items-center mb-8">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label" id="detail-symbol">
                                <span class="fs-1 fw-bold" id="detail-initial">T</span>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold mb-1" id="detail-name">Trial</h3>
                            <div class="fs-2 fw-bold text-primary" id="detail-price">GRATIS</div>
                        </div>
                    </div>
                    <!--end::Header-->
                    
                    <!--begin::Description-->
                    <div class="mb-8">
                        <h5 class="fw-bold mb-3">Deskripsi Paket</h5>
                        <p class="text-gray-600" id="detail-description">
                            Paket trial gratis untuk mencoba fasilitas gym selama 1 minggu
                        </p>
                    </div>
                    <!--end::Description-->
                    
                    <!--begin::Features-->
                    <div class="mb-8">
                        <h5 class="fw-bold mb-5">Fitur & Benefit</h5>
                        <div class="row g-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">Durasi Paket</div>
                                        <div class="text-gray-600" id="detail-duration">7 hari</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">Maksimal Kunjungan</div>
                                        <div class="text-gray-600" id="detail-visits">3 kali</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">Akses Fasilitas</div>
                                        <div class="text-gray-600">Semua peralatan gym</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="svg-icon svg-icon-2 svg-icon-primary me-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">Konsultasi Trainer</div>
                                        <div class="text-gray-600">Gratis konsultasi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Features-->
                    
                    <!--begin::Benefits-->
                    <div class="mb-8">
                        <h5 class="fw-bold mb-5">Benefit Tambahan</h5>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Akses ke semua peralatan gym</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Kelas grup gratis</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Konsultasi dengan trainer profesional</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Akses ke area sauna dan kolam renang</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Benefits-->
                    
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <a href="#" class="btn btn-primary" id="detail-select-btn">
                            <span id="detail-btn-text">Ambil Trial</span>
                        </a>
                    </div>
                    <!--end::Actions-->
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Packet Detail-->

@endsection

@section('script')
<script>
    // Search functionality
    $('[data-kt-packet-table-filter="search"]').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.col-md-6.col-xl-4').each(function() {
            const packetName = $(this).find('.fs-4.fw-bold').text().toLowerCase();
            const packetDesc = $(this).find('.fs-7.fw-semibold.text-gray-600').text().toLowerCase();
            
            if (packetName.includes(searchTerm) || packetDesc.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Show packet detail function
    function showPacketDetail(id) {
        // Show loading state
        const modal = $('#kt_modal_packet_detail');
        modal.modal('show');
        
        // Fetch packet details
        $.ajax({
            url: "{{ route('packet_membership.show', '') }}/" + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const packet = response.data;
                    
                    // Update modal content
                    $('#detail-modal-title').text('Detail Paket ' + packet.name);
                    $('#detail-name').text(packet.name);
                    $('#detail-initial').text(packet.name.charAt(0));
                    $('#detail-description').text(packet.description);
                    $('#detail-duration').text(packet.duration_days + ' hari');
                    $('#detail-visits').text(packet.max_visits == 999 ? 'Unlimited' : packet.max_visits + ' kali');
                    
                    // Update price
                    if (packet.price == 0) {
                        $('#detail-price').html('<span class="text-success">GRATIS</span>');
                    } else {
                        $('#detail-price').text('Rp ' + new Intl.NumberFormat('id-ID').format(packet.price));
                    }
                    
                    // Update symbol color
                    const symbolClass = packet.name == 'Trial' ? 'bg-info text-inverse-info' : 
                                       packet.name == 'Silver' ? 'bg-secondary text-inverse-secondary' : 
                                       packet.name == 'Gold' ? 'bg-warning text-inverse-warning' : 
                                       packet.name == 'Platinum' ? 'bg-primary text-inverse-primary' : 
                                       'bg-success text-inverse-success';
                    
                    $('#detail-symbol').attr('class', 'symbol-label ' + symbolClass);
                    
                    // Update button
                    const btnText = packet.name == 'Trial' ? 'Ambil Trial' : 'Pilih Paket';
                    const btnClass = packet.name == 'Trial' ? 'btn-info' : 
                                    packet.name == 'Silver' ? 'btn-secondary' : 
                                    packet.name == 'Gold' ? 'btn-warning' : 
                                    packet.name == 'Platinum' ? 'btn-primary' : 
                                    'btn-success';
                    
                    $('#detail-btn-text').text(btnText);
                    $('#detail-select-btn').attr('class', 'btn ' + btnClass);
                    $('#detail-select-btn').attr('href', "{{ route('packet_membership.select', '') }}/" + packet.id);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching packet details:', error);
                Swal.fire({
                    text: "Gagal memuat detail paket",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
                modal.modal('hide');
            }
        });
    }
</script>
@endsection
