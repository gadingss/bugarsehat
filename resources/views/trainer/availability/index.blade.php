@extends('layouts.app')

@section('content')
    <div class="row g-5 g-xxl-8">
        <!-- Form Tambah Ketersediaan -->
        <div class="col-xl-4">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Tambah Jadwal Kosong</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Atur ketersediaan Anda bulan ini</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form action="{{ route('trainer.availability.store') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label class="form-label required">Hari</label>
                            <select name="day_of_week" class="form-select" required>
                                <option value="">Pilih Hari</option>
                                <option value="Monday">Senin</option>
                                <option value="Tuesday">Selasa</option>
                                <option value="Wednesday">Rabu</option>
                                <option value="Thursday">Kamis</option>
                                <option value="Friday">Jumat</option>
                                <option value="Saturday">Sabtu</option>
                                <option value="Sunday">Minggu</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary w-100">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Ketersediaan -->
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
                    </h3>
                </div>
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
                                    <th class="ps-4 min-w-150px rounded-start">Hari</th>
                                    <th class="min-w-125px">Jam Mulai</th>
                                    <th class="min-w-125px">Jam Selesai</th>
                                    <th class="min-w-125px">Status</th>
                                    <th class="min-w-50px text-end rounded-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($availabilities as $slot)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">
                                                @php
                                                    $days = [
                                                        'Monday' => 'Senin',
                                                        'Tuesday' => 'Selasa',
                                                        'Wednesday' => 'Rabu',
                                                        'Thursday' => 'Kamis',
                                                        'Friday' => 'Jumat',
                                                        'Saturday' => 'Sabtu',
                                                        'Sunday' => 'Minggu'
                                                    ];
                                                    echo $days[$slot->day_of_week] ?? $slot->day_of_week;
                                                @endphp
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-dark fw-bold d-block mb-1 fs-6">{{ $slot->start_time->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-dark fw-bold d-block mb-1 fs-6">{{ $slot->end_time->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $slot->is_available ? 'badge-light-success' : 'badge-light-warning' }}">
                                                {{ $slot->is_available ? 'Tersedia' : 'Dipesan' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('trainer.availability.destroy', $slot->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus slot kosong ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada slot ketersediaan waktu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection