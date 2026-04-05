@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-5 g-xl-6">
        <div class="col-12">
            <!-- Header Section -->
            <div class="card mb-5 mb-xl-8 membership-status text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body py-8">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-70px symbol-circle me-5">
                            <span class="symbol-label bg-white bg-opacity-20">
                                <i class="fas fa-calendar-alt fs-2x text-white"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <h1 class="text-white fw-bold mb-1">{{ $config['title'] }}</h1>
                            <p class="text-white text-opacity-75 fs-6 mb-0">Lihat semua agenda latihan dan kelas grup Anda di satu tempat</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule List -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Agenda Terdekat</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Menampilkan sesi latihan Anda yang akan datang</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2">
                             <span class="badge badge-light-info">Kelas Grup</span>
                             <span class="badge badge-light-success">Personal Training</span>
                        </div>
                    </div>
                </div>
                <div class="card-body py-3">
                    @if($schedules->isEmpty())
                        <div class="text-center py-20">
                            <div class="mb-5">
                                <i class="fas fa-calendar-times fs-5x text-gray-300"></i>
                            </div>
                            <h3 class="fw-bold text-gray-600">Terima kasih, belum ada jadwal sesi hari ini</h3>
                            <p class="text-gray-400 fs-6">Silakan booking kelas grup atau layanan personal training untuk mulai berlatih.</p>
                            <div class="mt-5">
                                <a href="{{ route('services.index') }}" class="btn btn-primary me-2">Book Layanan</a>
                                <a href="{{ route('member.booking.index') }}" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary">Daftar Kelas</a>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4 min-w-150px rounded-start">Tipe & Nama</th>
                                        <th class="min-w-150px">Trainer</th>
                                        <th class="min-w-150px">Waktu</th>
                                        <th class="min-w-200px">Topik/Detail</th>
                                        <th class="min-w-100px text-end pe-4 rounded-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-3">
                                                        <span class="symbol-label bg-light-{{ $item['color'] }} text-{{ $item['color'] }}">
                                                            <i class="fas {{ $item['type'] == 'Group Class' ? 'fa-users' : 'fa-user' }} fs-4"></i>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark fw-bold text-hover-primary fs-6">{{ $item['name'] }}</span>
                                                        <span class="text-muted fw-semibold fs-7">{{ $item['type'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $item['trainer'] }}</span>
                                                    <span class="text-muted fs-7">Trainer Professional</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ \Carbon\Carbon::parse($item['start_time'])->format('d M Y') }}</span>
                                                    <span class="text-primary fw-bold fs-7">{{ \Carbon\Carbon::parse($item['start_time'])->format('H:i') }} WIB</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-gray-600 fw-semibold fs-7 d-block">{{ Str::limit($item['topic'], 50) }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge badge-light-{{ $item['color'] }} fs-7 fw-bold">{{ ucfirst($item['status']) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Information Section -->
            <div class="row mt-6">
                <div class="col-md-6">
                    <div class="card bg-light-info border-0 shadow-none mb-5 h-100">
                        <div class="card-body p-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-info-circle text-info fs-3 me-3"></i>
                                <h4 class="text-info-emphasis fw-bold mb-0">Tentang Jadwal Anda</h4>
                            </div>
                            <p class="text-gray-700 fs-7 mb-0">
                                Jadwal ini mencakup semua kelas grup yang Anda pilih dan sesi privat bersama personal trainer Anda. 
                                Pastikan hadir 10-15 menit sebelum waktu mulai untuk melakukan pemanasan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light-primary border-0 shadow-none mb-5 h-100">
                        <div class="card-body p-6 text-center d-flex flex-column justify-content-center">
                            <h4 class="fw-bold text-primary mb-3">Butuh Jadwal Baru?</h4>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('services.index') }}" class="btn btn-sm btn-primary">Book Personal Trainer</a>
                                <a href="{{ route('member.booking.index') }}" class="btn btn-sm btn-light-primary">Cari Kelas Grup</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
