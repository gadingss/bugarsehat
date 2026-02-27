@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">Rekaman kemajuan latihan Anda yang dicatat oleh
                    trainer</span>
            </h3>
        </div>

        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-125px rounded-start">Tanggal</th>
                            <th class="min-w-200px">Trainer</th>
                            <th class="min-w-300px">Catatan Progress</th>
                            <th class="min-w-200px">Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($progressHistory as $progress)
                            <tr>
                                <td class="ps-4">
                                    <span
                                        class="text-dark fw-bold d-block mb-1 fs-6">{{ $progress->date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                {{ substr($progress->trainer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold mb-1 fs-6">{{ $progress->trainer->name }}</span>
                                            <span class="text-muted fs-7">Personal Trainer</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-medium d-block fs-6">{{ $progress->progress_note }}</span>
                                    @if($progress->special_note)
                                        <div class="badge badge-light-warning mt-1">Catatan Khusus: {{ $progress->special_note }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="text-muted fw-semibold d-block fs-7">{{ $progress->recommendation ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-10">
                                    <i class="fas fa-info-circle fs-2 mb-3 d-block"></i>
                                    Belum ada history progress yang dicatat untuk Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($progressHistory->hasPages())
                <div class="mt-4">
                    {{ $progressHistory->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection