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
                                            <span
                                                class="text-primary fw-bold d-block fs-6">{{ \Carbon\Carbon::parse($ub->scheduled_date)->format('d M Y') }}</span>
                                            <span
                                                class="text-muted fs-7">{{ \Carbon\Carbon::parse($ub->scheduled_date)->format('H:i') }}
                                                WIB</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge badge-light-primary">{{ ucfirst($ub->status) }}</span>
                                        </td>
                                    </tr>
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
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($b->status) }}</span>
                                        </td>
                                    </tr>
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
@endsection