@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📋 Check-in Report</h3>
        <div>
            <a href="{{ route('checkin.export.pdf') }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('checkin.export.excel') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama Member</th>
                    <th scope="col">Email</th>
                    <th scope="col">Paket Membership</th>
                    <th scope="col">Waktu Check-in</th>
                    <th scope="col">Waktu Check-out</th>
                    <th scope="col">Durasi</th>
                    <th scope="col">Staff</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checkins as $index => $checkin)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $checkin->user->name }}</td>
                    <td>{{ $checkin->user->email }}</td>
                    <td>{{ $checkin->membership->package->name ?? '-' }}</td>
                    <td>{{ $checkin->checkin_time->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($checkin->checkout_time)
                            {{ $checkin->checkout_time->format('d/m/Y H:i') }}
                        @else
                            <span class="text-warning">Belum checkout</span>
                        @endif
                    </td>
                    <td>
                        @if($checkin->checkout_time)
                            {{ $checkin->getFormattedDuration() }}
                        @else
                            <span class="text-warning">-</span>
                        @endif
                    </td>
                    <td>
                        @if($checkin->staff)
                            <span class="badge bg-success">{{ $checkin->staff->name }}</span>
                        @else
                            <span class="badge bg-info">Mandiri</span>
                        @endif
                    </td>
                    <td>
                        @if($checkin->checkout_time)
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-warning">Aktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Tidak ada data check-in tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
