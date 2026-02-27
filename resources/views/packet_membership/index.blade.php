@extends('layouts.app')

@section('title', 'Paket Membership')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
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
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-packet-table-toolbar="base">
                    @if(auth()->user()->hasRole('staff') || auth()->user()->hasRole('owner'))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_packet">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                      transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                      fill="currentColor" />
                            </svg>
                        </span>
                        Tambah Paket
                    </button>
                    @endif
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Packets Grid-->
                <div class="row g-6 g-xl-9">
                    @forelse($packets as $packet)
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <div class="card h-100">
                                <!--begin::Card header-->
                                <div class="card-header flex-nowrap border-0 pt-9">
                                    <div class="card-title m-0">
                                        <div class="symbol symbol-45px w-45px bg-light me-5">
                                            <span class="symbol-label bg-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} text-inverse-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }}">
                                                <span class="fs-2x fw-bold">{{ substr($packet->name, 0, 1) }}</span>
                                            </span>
                                        </div>
                                        <a href="#" class="fs-4 fw-bold text-hover-primary text-gray-600 m-0">{{ $packet->name }}</a>
                                    </div>
                                    <div class="card-toolbar m-0">
                                        @if(!auth()->user()->hasRole('member'))
                                            <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect x="10" y="10" width="4" height="4" rx="2" fill="currentColor"/>
                                                        <rect x="17" y="10" width="4" height="4" rx="2" fill="currentColor"/>
                                                        <rect x="3" y="10" width="4" height="4" rx="2" fill="currentColor"/>
                                                    </svg>
                                                </span>
                                            </button>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Actions</div>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('packet_membership.edit', $packet->id) }}" class="menu-link px-3">Edit</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" onclick="deletePacket({{ $packet->id }})">Delete</a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body d-flex flex-column px-9 pt-6 pb-8">
                                    <!--begin::Heading-->
                                    <div class="fs-2tx fw-bold mb-3">
                                        @if($packet->price == 0)
                                            <span class="text-success">GRATIS</span>
                                        @else
                                            Rp {{ number_format($packet->price, 0, ',', '.') }}
                                        @endif
                                    </div>
                                    <!--end::Heading-->
                                    
                                    <!--begin::Description-->
                                    <div class="fs-7 fw-semibold text-gray-600 mb-7">
                                        {{ $packet->description }}
                                    </div>
                                    <!--end::Description-->
                                    
                                    <!--begin::Features-->
                                    <div class="d-flex flex-column text-gray-600">
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            Durasi: {{ $packet->duration_days }} hari
                                        </div>
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            Maksimal kunjungan: {{ $packet->max_visits == 999 ? 'Unlimited' : $packet->max_visits . ' kali' }}
                                        </div>
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>
                                            Akses semua fasilitas
                                        </div>
                                    </div>
                                    <!--end::Features-->
                                    
                                    <!--begin::Select-->
                                    <div class="d-flex gap-2 mt-auto">
                                        <button type="button" class="btn btn-sm btn-light-primary flex-fill" onclick="showPacketDetail({{ $packet->id }})">
                                            Lihat Detail
                                        </button>
                                        <a href="{{ route('packet_membership.select', $packet->id) }}" class="btn btn-sm btn-{{ $packet->name == 'Trial' ? 'info' : ($packet->name == 'Silver' ? 'secondary' : ($packet->name == 'Gold' ? 'warning' : ($packet->name == 'Platinum' ? 'primary' : 'success'))) }} flex-fill">
                                            {{ $packet->name == 'Trial' ? 'Ambil Trial' : 'Pilih Paket' }}
                                        </a>
                                    </div>
                                    <!--end::Select-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-10">
                                <div class="fs-1 fw-bold text-gray-400 mb-3">Tidak ada paket</div>
                                <div class="fs-6 text-gray-600">Belum ada paket membership yang tersedia</div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <!--end::Packets Grid-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>

@if(!auth()->user()->hasRole('member'))
<!--begin::Modal - Add/Edit Packet-->
<div class="modal fade" id="kt_modal_add_packet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_packet_header">
                <h2 class="fw-bold">{{ isset($packet) ? 'Edit' : 'Tambah' }} Paket Membership</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-packets-modal-action="close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_packet_form" class="form" action="{{ isset($packet) ? route('packet_membership.update', $packet->id ?? '') : route('packet_membership.store') }}" method="POST">
                    @csrf
                    @if(isset($packet))
                        @method('PUT')
                    @endif
                    
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_packet_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_packet_header" data-kt-scroll-wrappers="#kt_modal_add_packet_scroll" data-kt-scroll-offset="300px">
                        
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nama Paket</label>
                            <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Nama paket membership" value="{{ old('name', $packet->name ?? '') }}" required/>
                            @error('name')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Harga</label>
                            <input type="number" name="price" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="0" value="{{ old('price', $packet->price ?? '') }}" min="0" required/>
                            @error('price')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Durasi (Hari)</label>
                                <input type="number" name="duration_days" class="form-control form-control-solid" placeholder="30" value="{{ old('duration_days', $packet->duration_days ?? '') }}" min="1" required/>
                                @error('duration_days')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Maksimal Kunjungan</label>
                                <input type="number" name="max_visits" class="form-control form-control-solid" placeholder="20" value="{{ old('max_visits', $packet->max_visits ?? '') }}" min="1" required/>
                                @error('max_visits')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="Deskripsi paket membership">{{ old('description', $packet->description ?? '') }}</textarea>
                            @error('description')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-packets-modal-action="cancel">Batal</button>
                        <button type="submit" class="btn btn-primary" data-kt-packets-modal-action="submit">
                            <span class="indicator-label">{{ isset($packet) ? 'Update' : 'Simpan' }}</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Add/Edit Packet-->
@endif

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
                                        <div class="fw-semibold">Konsultasi</div>
                                        <div class="text-gray-600">Gratis konsultasi trainer</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Features-->
                    
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

<!-- Delete Form -->
@if(!auth()->user()->hasRole('member'))
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection

@section('script')
<script>
    // Auto open modal if editing
    @if(isset($packet) && !auth()->user()->hasRole('member'))
        $('#kt_modal_add_packet').modal('show');
    @endif

    // Close modal handlers
    @if(!auth()->user()->hasRole('member'))
    $('[data-kt-packets-modal-action="close"], [data-kt-packets-modal-action="cancel"]').on('click', function() {
        $('#kt_modal_add_packet').modal('hide');
        if (window.location.href.includes('/edit')) {
            window.location.href = "{{ route('packet_membership') }}";
        }
    });
    @endif

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

    // Delete function
    @if(!auth()->user()->hasRole('member'))
    function deletePacket(id) {
        Swal.fire({
            text: "Apakah Anda yakin ingin menghapus paket ini?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function (result) {
            if (result.value) {
                const form = document.getElementById('delete-form');
                form.action = "{{ route('packet_membership.destroy', '') }}/" + id;
                form.submit();
            }
        });
    }
    @endif

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
</script>
@endsection
