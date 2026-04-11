@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('trainer.schedule.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Open Kelas Baru
                </a>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body py-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="GET" action="{{ route('trainer.schedule.index') }}" class="row g-3 bg-light p-4 rounded mb-7 bg-opacity-50">
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-bold">Jenis Sesi</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="class" {{ request('type') == 'class' ? 'selected' : '' }}>Latihan Bersama (Class)</option>
                        <option value="pt" {{ request('type') == 'pt' ? 'selected' : '' }}>Personal Training (PT)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-bold">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-bold">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-info w-100 flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->has('type'))
                        <a href="{{ route('trainer.schedule.index') }}" class="btn btn-sm btn-light-danger ms-2 px-3" title="Reset Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="row g-6 g-xl-9">
                @forelse($allSchedules as $item)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border border-2 border-gray-300 border-hover-primary h-100 shadow-sm">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title m-0">
                                    <div class="symbol symbol-50px w-50px bg-light">
                                        <span
                                            class="symbol-label fs-2 fw-bold {{ $item->type == 'Class' ? 'text-primary' : 'text-success' }}">
                                            {{ $item->type == 'Class' ? 'C' : 'PT' }}
                                        </span>
                                    </div>
                                    <div class="ms-3">
                                        <div class="fs-5 fw-bold text-gray-900">{{ $item->title }}</div>
                                        <div class="text-muted fs-7">
                                            {{ $item->type == 'Class' ? 'Latihan Bersama' : 'Personal Training' }}</div>
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <span
                                        class="badge {{ $item->status == 'Completed' ? 'badge-light-success' : 'badge-light-primary' }} fw-bold px-3 py-2">
                                        {{ $item->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body border-0 pt-0 pb-5">
                                <div class="bg-light-secondary p-3 rounded mb-4">
                                    <p class="text-gray-600 fw-semibold fs-7 mb-0">
                                        {{ Str::limit($item->description, 100) }}
                                    </p>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-30px me-3">
                                        <div class="symbol-label bg-light">
                                            <i class="fas fa-calendar-day text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $item->start_time->format('d M Y') }}</span>
                                        <span class="fs-8 text-muted">{{ $item->start_time->format('H:i') }} -
                                            {{ $item->end_time->format('H:i') }}</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30px me-3">
                                        <div class="symbol-label bg-light">
                                            <i class="fas fa-user-friends text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $item->member_info }}</span>
                                        <span
                                            class="fs-8 text-muted">{{ $item->type == 'Class' ? 'Kapasitas' : 'Nama Member' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer border-0 pt-0 pb-5">
                                <div class="d-flex gap-2">
                                    @if($item->type == 'Class')
                                        @if(isset($item->item_count) && $item->item_count > 1)
                                            <a href="{{ route('trainer.schedule.show', $item->id) }}"
                                                class="btn btn-sm btn-light-info flex-grow-1">
                                                <i class="fas fa-list me-1"></i> Detail {{ $item->item_count }} Sesi
                                            </a>
                                        @else
                                            <a href="{{ route('trainer.schedule.edit', $item->id) }}"
                                                class="btn btn-sm btn-light-info flex-grow-1">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('service_transaction.show', $item->id) }}"
                                            class="btn btn-sm btn-light-success flex-grow-1">
                                            <i class="fas fa-eye me-1"></i> Detail Session
                                        </a>
                                    @endif
                                    @if($item->type == 'Class')
                                        <form action="{{ route('trainer.schedule.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('{{ isset($item->item_count) && $item->item_count > 1 ? 'Hapus seluruh ' . $item->item_count . ' sesi pertemuan untuk kelas ini?' : 'Hapus jadwal ini?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger flex-grow-1">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card card-flush py-10 shadow-sm border border-dashed border-gray-300">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-times fs-4x text-gray-200 mb-5"></i>
                                <h4 class="text-gray-600">Belum ada jadwal ketersediaan atau booking PT.</h4>
                                <p class="text-muted">Jadwal Anda akan muncul di sini setelah ada kelas yang dibuat atau member
                                    melakukan booking PT.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
        <!--end::Body-->
    </div>
@endsection