@extends('layouts.app')

@section('content')
    <div class="row g-5 g-xl-6">
        <!-- Upcoming Sessions (Services/Personal Training) -->
        <div class="col-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Jadwal Personal Training & Layanan</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Sesi yang telah dijadwalkan dengan trainer
                            Anda</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('services.index') }}" class="btn btn-sm btn-light-primary">
                            <i class="fas fa-plus"></i> Book Layanan Baru
                        </a>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 min-w-150px rounded-start">Layanan</th>
                                    <th class="min-w-150px">Trainer</th>
                                    <th class="min-w-150px">Jadwal</th>
                                    <th class="min-w-100px text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingBookings as $ub)
                                    <tr>
                                        <td class="ps-4">
                                            <span
                                                class="text-dark fw-bold d-block fs-6">{{ $ub->service->name ?? 'Layanan' }}</span>
                                            <span class="text-muted fs-7">ID: {{ $ub->id }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-gray-800 fw-bold d-block fs-6">{{ $ub->trainer->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $pendingSessionsCount = $ub->serviceSessions->where('status', 'pending')->whereNull('scheduled_date')->count();
                                                $totalSessionsCount = $ub->serviceSessions->count();
                                            @endphp

                                            @if($ub->scheduled_date)
                                                <span class="text-primary fw-bold d-block fs-6">{{ \Carbon\Carbon::parse($ub->scheduled_date)->format('d M Y') }}</span>
                                                <span class="text-muted fs-7">{{ \Carbon\Carbon::parse($ub->scheduled_date)->format('H:i') }} WIB</span>
                                            @elseif($totalSessionsCount > 0)
                                                @if($pendingSessionsCount > 0)
                                                    <span class="badge badge-light-success fw-bold fs-7 mb-1 px-3 py-2"><i class="fas fa-ticket-alt text-success me-1"></i> Kuota Belum Diklaim</span>
                                                    <span class="text-muted fs-8 d-block mb-3">Siap dijadwalkan untuk {{ $totalSessionsCount }} sesi kelas penuh</span>
                                                    <button class="btn btn-sm btn-primary w-100" onclick="claimQuota({{ $ub->id }}, {{ $ub->service_id }})">
                                                        Pilih Jadwal Kelas
                                                    </button>
                                                @else
                                                    <span class="badge badge-light-secondary fw-bold fs-7 mb-1 px-3 py-2"><i class="fas fa-calendar-check text-muted me-1"></i> Terjadwal Penuh</span>
                                                    <span class="text-muted fs-8 d-block">Seluruh rangkaian sesi pembelajaran ini telah dijadwalkan.</span>
                                                @endif
                                            @else
                                                <span class="text-warning fw-bold d-block fs-6">Status Menunggu</span>
                                                <span class="text-muted fs-7 mb-1">Belum digenerate</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge badge-light-primary mb-2">{{ ucfirst($ub->status) }}</span>
                                            @if($ub->serviceSessions->count() > 0)
                                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary w-30px h-30px" type="button" data-bs-toggle="collapse" data-bs-target="#sessions-ub-{{ $ub->id }}">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($ub->serviceSessions->count() > 0)
                                    <tr id="sessions-ub-{{ $ub->id }}" class="collapse bg-light-secondary">
                                        <td colspan="4" class="px-6 py-4">
                                            <div class="fw-bold fs-6 mb-3 text-dark">Detail Sesi Pertemuan</div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless table-striped align-middle gs-0 gy-2 mb-0">
                                                    <thead>
                                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                            <th>Sesi</th>
                                                            <th>Topik</th>
                                                            <th>Tanggal</th>
                                                            <th>Kehadiran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-semibold fs-7">
                                                        @foreach($ub->serviceSessions as $session)
                                                            <tr>
                                                                <td class="ps-2">Sesi {{ $session->session_number }}</td>
                                                                <td>{{ $session->topic ?: '-' }}</td>
                                                                <td>{{ $session->scheduled_date ? \Carbon\Carbon::parse($session->scheduled_date)->format('d M Y H:i') : '-' }}</td>
                                                                <td>
                                                                    @if($session->status == 'attended')
                                                                        <span class="badge badge-light-success py-1 px-2">Hadir</span>
                                                                    @elseif($session->status == 'cancelled')
                                                                        <span class="badge badge-light-danger py-1 px-2">Batal</span>
                                                                    @else
                                                                        <span class="badge badge-light-warning py-1 px-2">Pending</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-10">
                                            Belum ada jadwal layanan mendatang.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History (All Services) -->
        <div class="col-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Riwayat Transaksi Layanan</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Seluruh riwayat pembelian layanan dan personal
                            training Anda</span>
                    </h3>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 min-w-150px rounded-start">Layanan</th>
                                    <th class="min-w-125px">Tanggal Transaksi</th>
                                    <th class="min-w-125px">Jumlah</th>
                                    <th class="min-w-100px text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $b)
                                    <tr>
                                        <td class="ps-4">
                                            <span
                                                class="text-dark fw-bold d-block fs-6">{{ $b->service->name ?? 'Layanan' }}</span>
                                            <span class="text-muted fs-7">ID: {{ $b->id }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted fw-semibold d-block fs-7">{{ \Carbon\Carbon::parse($b->transaction_date)->format('d M Y, H:i') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold d-block fs-6">Rp
                                                {{ number_format($b->amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            @php
                                                $badgeClass = 'badge-light-primary';
                                                if ($b->status == 'completed' || $b->status == 'scheduled')
                                                    $badgeClass = 'badge-light-success';
                                                if ($b->status == 'pending')
                                                    $badgeClass = 'badge-light-warning';
                                                if ($b->status == 'cancelled')
                                                    $badgeClass = 'badge-light-danger';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} mb-2">{{ ucfirst($b->status) }}</span>
                                            @if($b->status == 'scheduled' && $b->serviceSessions->count() > 0)
                                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary w-30px h-30px" type="button" data-bs-toggle="collapse" data-bs-target="#sessions-b-{{ $b->id }}">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($b->status == 'scheduled' && $b->serviceSessions->count() > 0)
                                    <tr id="sessions-b-{{ $b->id }}" class="collapse bg-light-secondary">
                                        <td colspan="4" class="px-6 py-4">
                                            <div class="fw-bold fs-6 mb-3 text-dark">Riwayat Sesi Latihan</div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless table-striped align-middle gs-0 gy-2 mb-0">
                                                    <thead>
                                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                            <th>Sesi</th>
                                                            <th>Topik</th>
                                                            <th>Tanggal</th>
                                                            <th>Kehadiran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-semibold fs-7">
                                                        @foreach($b->serviceSessions as $session)
                                                            <tr>
                                                                <td class="ps-2">Sesi {{ $session->session_number }}</td>
                                                                <td>{{ $session->topic ?: '-' }}</td>
                                                                <td>{{ $session->scheduled_date ? \Carbon\Carbon::parse($session->scheduled_date)->format('d M Y H:i') : '-' }}</td>
                                                                <td>
                                                                    @if($session->status == 'attended')
                                                                        <span class="badge badge-light-success py-1 px-2">Hadir</span>
                                                                    @elseif($session->status == 'cancelled')
                                                                        <span class="badge badge-light-danger py-1 px-2">Batal</span>
                                                                    @else
                                                                        <span class="badge badge-light-warning py-1 px-2">Pending</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-10">
                                            Belum ada riwayat transaksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($bookings->hasPages())
                        <div class="mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Booking Quota -->
    <div class="modal fade" id="bookingQuotaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Gunakan Kuota Layanan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="booking-quota-form">
                        @csrf
                        <input type="hidden" id="quota-transaction-id" name="transaction_id">
                        <input type="hidden" id="quota-service-id" name="service_id">

                        <div class="mb-3">
                            <label class="form-label border-top pt-3 w-100">Pilih Jadwal Kelas Tersedia</label>
                            <select class="form-select" name="schedule_id" id="quota-schedule-select" required>
                                <option value="">Pilih Jadwal</option>
                            </select>
                            <div class="form-text mt-1 text-muted"><i class="fas fa-info-circle me-1"></i>Hanya menampilkan kelas yang dibuka oleh trainer dan masih memiliki kuota.</div>
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
        function claimQuota(transactionId, serviceId) {
            document.getElementById('quota-transaction-id').value = transactionId;
            document.getElementById('quota-service-id').value = serviceId;
            
            const select = document.getElementById('quota-schedule-select');
            select.innerHTML = '<option value="">Memuat jadwal...</option>';
            
            fetch(`/services/${serviceId}/schedules`, {
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

            new bootstrap.Modal('#bookingQuotaModal').show();
        }

        document.getElementById('booking-quota-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(this).entries());
            const url = `{{ route('services.claim-quota') }}`;
            
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
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memproses booking');
                return data;
            })
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('Gagal: ' + error.message);
            });
        });
    </script>
@endsection