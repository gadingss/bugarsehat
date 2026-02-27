@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
            </h3>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-300px rounded-start">Member Info</th>
                            <th class="min-w-125px">Phone</th>
                            <th class="min-w-125px">Joined At</th>
                            <th class="min-w-200pxtext-end rounded-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-primary text-primary fs-3 fw-bold">
                                                {{ substr($member->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="#"
                                                class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $member->name }}</a>
                                            <span
                                                class="text-muted fw-semibold text-muted d-block fs-7">{{ $member->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="#"
                                        class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">{{ $member->phone ?? '-' }}</a>
                                </td>
                                <td>
                                    <a href="#"
                                        class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">{{ $member->created_at->format('d M Y') }}</a>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('trainer.progress.create', ['member_id' => $member->id]) }}"
                                        class="btn btn-sm btn-light-primary">
                                        <i class="fas fa-plus"></i> Add Progress
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada member yang ditugaskan kepada Anda.
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