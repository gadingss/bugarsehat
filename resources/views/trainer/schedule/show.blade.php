@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('trainer.schedule.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                </a>
            </div>
        </div>

        <div class="card-body py-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4 table-row-dashed">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-100px rounded-start">No. Sesi</th>
                            <th class="min-w-200px">Judul Sesi</th>
                            <th class="min-w-200px">Tanggal & Waktu</th>
                            <th class="min-w-100px">Kapasitas</th>
                            <th class="min-w-100px text-end rounded-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batchSchedules as $index => $subSchedule)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-dark fw-bold d-block fs-6">Sesi {{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">{{ $subSchedule->title }}</span>
                                    <span class="text-muted fw-semibold d-block fs-7">{{ Str::limit($subSchedule->description, 50) }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">
                                        {{ $subSchedule->start_time->format('d M Y') }}
                                    </span>
                                    <span class="text-muted fw-semibold d-block fs-7">
                                        {{ $subSchedule->start_time->format('H:i') }} - {{ $subSchedule->end_time->format('H:i') }} WIB
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fs-7">
                                        {{ $subSchedule->bookings->count() }} / {{ $subSchedule->capacity }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('trainer.schedule.edit', $subSchedule->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                        <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </a>
                                    <form action="{{ route('trainer.schedule.destroy', ['schedule' => $subSchedule->id, 'single' => 1]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus 1 sesi spesifik ini dari rangkaian jadwal?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 border-top pt-5 d-flex justify-content-end">
                <form action="{{ route('trainer.schedule.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus SELURUH {{ count($batchSchedules) }} pertemuan di kelas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i> Hapus Seluruh Batch ({!! count($batchSchedules) !!} Sesi)
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
