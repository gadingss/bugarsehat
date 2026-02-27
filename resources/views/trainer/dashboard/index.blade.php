@extends('layouts.app')

@section('content')
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-4">
            <div class="card card-flush h-md-100 shadow-sm">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['upcoming_classes'] ?? 0 }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">Upcoming Classes</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-end pe-0">
                    <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Activities this week</span>
                    <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Class Schedule">
                            <span class="symbol-label bg-light-primary text-primary fw-bold">C</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-md-100 shadow-sm">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['upcoming_pt'] ?? 0 }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">Upcoming PT Sessions</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-end pe-0">
                    <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Personal Training</span>
                    <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="PT Sessions">
                            <span class="symbol-label bg-light-success text-success fw-bold">PT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-md-100 shadow-sm">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['total_students'] ?? 0 }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">Total Students Managed</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-row align-items-center justify-content-between">
                    <div class="d-flex flex-column">
                        <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Active Students</span>
                    </div>
                    <i class="fas fa-users fs-2tx text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <h3 class="card-label fw-bold text-dark">Upcoming Schedule</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('trainer.schedule.index') }}" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i>Manage All Schedules
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Date</th>
                            <th class="min-w-100px">Time</th>
                            <th class="min-w-150px">Activity</th>
                            <th class="min-w-100px">Type</th>
                            <th class="min-w-150px">Member</th>
                            <th class="text-end min-w-70px">Status</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @forelse($upcomingSchedule as $item)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold">{{ $item['date'] }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fs-7 fw-bold">{{ $item['time'] }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold text-hover-primary fs-6">{{ $item['name'] }}</span>
                                </td>
                                <td>
                                    @if($item['type'] == 'Class')
                                        <span class="badge badge-light-info">Class</span>
                                    @else
                                        <span class="badge badge-light-success">Personal Training</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px symbol-circle me-3">
                                            <div class="symbol-label fs-8 fw-bold bg-light-warning text-warning">
                                                {{ substr($item['member'], 0, 1) }}
                                            </div>
                                        </div>
                                        <span class="text-gray-800 fw-bold">{{ $item['member'] }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="badge badge-light-warning">{{ $item['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-calendar-check fs-3x text-gray-200 mb-3"></i>
                                        <span class="text-gray-400">No classes or PT sessions scheduled.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection