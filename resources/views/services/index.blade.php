@extends('layouts.app')
@section('title', 'Layanan Member')

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
        <!-- Header -->
        <div class="row g-5 g-xl-6 mb-5">
            <div class="col-12">
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="fw-bold">Layanan Member</h2>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Search -->
                                <div class="position-relative">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4 translate-middle-y top-50">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-solid w-250px ps-14"
                                        placeholder="Cari layanan..." id="search-input" />
                                </div>
                                <!-- Category Filter -->
                                <select class="form-select form-select-solid w-150px" id="category-filter">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <!-- Booking Filter -->
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="booking-filter">
                                    <label class="form-check-label" for="booking-filter">
                                        Perlu Booking
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="row g-5 g-xl-6" id="services-container">
            @forelse($services as $service)
                <div class="col-12 col-md-6 col-lg-4 service-item" data-category="{{ $service->category }}"
                    data-booking="{{ $service->requires_booking ? '1' : '0' }}" data-name="{{ strtolower($service->name) }}">
                    <div class="card card-flush h-100">
                        <div class="position-relative overflow-hidden rounded-top" style="height: 200px;">
                            <img src="{{ $service->image ? asset('storage/' . $service->image) : asset('metronic/assets/media/stock/600x400/img-2.jpg') }}"
                                 alt="{{ $service->name }}"
                                 class="w-100 h-100"
                                 style="object-fit: cover;"
                                 onerror="this.src='{{ asset('metronic/assets/media/stock/600x400/img-2.jpg') }}'">
                            @if($service->price == 0)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge badge-success fs-7">GRATIS</span>
                                </div>
                            @endif
                            @if($service->requires_booking)
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge badge-warning fs-7">Perlu Booking</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-2">{{ $service->name }}</h4>
                                <p class="text-gray-600 fs-7 mb-3">{{ Str::limit($service->description, 80) }}</p>
                            </div>
                            <div class="mb-4">
                                @if($service->price == 0)
                                    <span class="fs-2 fw-bold text-success">GRATIS</span>
                                @else
                                    <span class="fs-2 fw-bold text-primary">Rp
                                        {{ number_format($service->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light-primary flex-fill" onclick="viewService({{ $service->id }})">
                                    <i class="fas fa-eye me-2"></i>Detail
                                </button>
                                @if($service->requires_booking)
                                    <button class="btn btn-primary flex-fill" onclick="bookService({{ $service->id }})">
                                        <i class="fas fa-calendar-plus me-2"></i>Booking
                                    </button>
                                @else
                                    <button class="btn btn-success flex-fill" onclick="useService({{ $service->id }})">
                                        <i class="fas fa-play me-2"></i>Gunakan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">Tidak ada layanan</div>
            @endforelse
        </div>

        @if($services->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $services->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="serviceDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal-service-name"></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-service-content"></div>
            </div>
        </div>
    </div>

    <!-- Modal Booking -->
    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Booking Layanan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="booking-form">
                        @csrf
                        <input type="hidden" id="service-id" name="service_id">

                        <div class="mb-3">
                            <label class="form-label">Pilih Trainer (Opsional untuk Layanan Umum)</label>
                            <select class="form-select" name="trainer_id" id="trainer-select">
                                <option value="">Tidak Menggunakan Trainer Khusus</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1 text-muted"><i class="fas fa-info-circle me-1"></i>Pilih trainer
                                untuk jadwal <b class="text-primary">Personal Training</b>.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label border-top pt-3 w-100">Pilih Jadwal Kelas Tersedia</label>
                            <select class="form-select" name="schedule_id" id="schedule-select" required>
                                <option value="">Pilih Jadwal</option>
                            </select>
                            <div class="form-text mt-1 text-muted"><i class="fas fa-info-circle me-1"></i>Hanya menampilkan kelas yang dibuka oleh trainer dan masih memiliki kuota.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function viewService(id) {
            const modal = new bootstrap.Modal('#serviceDetailModal');
            document.getElementById('modal-service-content').innerHTML = 'Loading...';
            modal.show();
            fetch(`/services/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            console.error('Server error:', text);
                            throw new Error('Server returned error status ' + res.status);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('modal-service-name').textContent = data.service.name;
                        document.getElementById('modal-service-content').innerHTML = data.html;
                    } else {
                        document.getElementById('modal-service-content').innerHTML = 'Gagal memuat detail.';
                    }
                })
                .catch((err) => {
                    console.error('Fetch error:', err);
                    document.getElementById('modal-service-content').innerHTML = 'Error sistem: ' + err.message;
                });
        }

        function bookService(id) {
            document.getElementById('service-id').value = id;
            
            // Fetch schedules
            const select = document.getElementById('schedule-select');
            select.innerHTML = '<option value="">Memuat jadwal...</option>';
            
            fetch(`/services/${id}/schedules`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                select.innerHTML = '<option value="">Pilih Jadwal</option>';
                if (data.success && data.schedules.length > 0) {
                    data.schedules.forEach(schedule => {
                        select.innerHTML += `<option value="${schedule.id}">${schedule.text}</option>`;
                    });
                } else {
                    select.innerHTML = '<option value="">Tidak ada jadwal kelas terbuka</option>';
                }
            })
            .catch(err => {
                select.innerHTML = '<option value="">Gagal memuat jadwal</option>';
            });

            new bootstrap.Modal('#bookingModal').show();
        }

        document.getElementById('booking-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(this).entries());
            const url = `{{ route('services.book', ':id', false) }}`.replace(':id', formData.service_id);
            console.log('Booking attempt to:', url);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
                .then(async res => {
                    const text = await res.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Response is not JSON:', text);
                        throw new Error('Server returned invalid response. Check console.');
                    }

                    if (!res.ok) {
                        throw new Error(data.message || 'Gagal memproses booking');
                    }
                    return data;
                })
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            location.reload();
                        }
                    }
                })
                .catch((error) => {
                    console.error('Fetch error:', error);
                    alert('Gagal menghubungi server: ' + error.message);
                });
        });

        function addSessionTemplate(serviceId) {
            const tableBody = document.querySelector('#kt_table_templates tbody');
            const rowCount = tableBody.querySelectorAll('tr:not(#empty-template-row)').length;
            const nextNum = rowCount + 1;

            fetch(`/services/${serviceId}/templates`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ session_number: nextNum, topic: '' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        viewService(serviceId);
                    }
                });
        }

        function updateSessionTemplate(templateId, topic) {
            fetch(`/services/templates/${templateId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ topic: topic })
            });
        }

        function deleteSessionTemplate(templateId, btn, serviceId) {
            if (!confirm('Hapus template sesi ini?')) return;

            fetch(`/services/templates/${templateId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        viewService(serviceId);
                    }
                });
        }
        function previewServiceImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('service-new-img').src = e.target.result;
                    document.getElementById('service-image-preview-new').classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection