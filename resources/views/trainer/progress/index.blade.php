@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
            </h3>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                title="Click to add new progress">
                <a href="{{ route('trainer.progress.create') }}" class="btn btn-sm btn-light-primary">
                    <i class="fas fa-plus"></i> Input Progress
                </a>
            </div>
        </div>

        <!--begin::Body-->
        <div class="card-body py-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-125px rounded-start">Date</th>
                            <th class="min-w-200px">Member</th>
                            <th class="min-w-300px">Progress Note</th>
                            <th class="min-w-200px">Recommendations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($progresses as $progress)
                            <tr>
                                <td class="ps-4">
                                    <span
                                        class="text-dark fw-bold d-block mb-1 fs-6">{{ $progress->date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                {{ substr($progress->member->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span
                                                class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $progress->member->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="text-muted fw-semibold text-muted d-block fs-7">{{ $progress->progress_note }}</span>
                                    @if($progress->special_note)
                                        <div class="badge badge-light-warning mt-1">Note: {{ $progress->special_note }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="text-muted fw-semibold text-muted d-block fs-7">{{ $progress->recommendation ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada history progress.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Body-->
    </div>
@endsection