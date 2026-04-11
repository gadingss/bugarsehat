@extends('layouts.app')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7 mb-3">Monitor seluruh jadwal Personal Training maupun Kelas Terbuka untuk semua Trainer</span>
        </h3>
    </div>

    <!--begin::Body-->
    <div class="card-body py-3">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Jam / Waktu</th>
                        <th class="min-w-150px">Trainer</th>
                        <th class="min-w-150px">Jenis Kelas / Layanan</th>
                        <th class="min-w-200px">Member / Partisipan</th>
                        <th class="min-w-100px text-end pe-4 rounded-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingSessions as $session)
                        <tr>
                            <td class="ps-4">
                                <span class="text-dark fw-bold text-hover-primary mb-1 fs-6 pb-2">{{ $session['schedule_time']->format('d M Y') }}</span>
                                <span class="text-muted fw-semibold text-muted d-block fs-7"><i class="fas fa-clock fs-8 me-1 text-info"></i> {{ $session['schedule_time']->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-info text-info fw-bold">{{ substr($session['trainer_name'], 0, 1) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $session['trainer_name'] }}</span>
                                        <span class="text-muted fw-semibold d-block fs-8">Trainer</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark fw-bold d-block fs-6">{{ $session['service_name'] }}</span>
                                @if($session['type'] == 'Open Class')
                                    <span class="badge badge-light-primary fs-8">Open Class</span>
                                @else
                                    <span class="badge badge-light-warning fs-8">Personal Training</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ $session['member_name'] }}</span>
                                    <span class="text-muted fs-7">{{ $session['topic'] }}</span>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge badge-light-{{ $session['status_badge'] }} fw-semibold fs-7 px-3 py-2 text-uppercase">{{ $session['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-calendar-times fs-2x text-muted mb-4"></i>
                                    <span class="fw-semibold fs-5">Tidak ada jadwal sesi PT atau Kelas mendatang.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!--end::Body-->
</div>
@endsection
