@extends('layouts.app')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
        </h3>
    </div>

    <!--begin::Body-->
    <div class="card-body py-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="GET" action="{{ route('trainer.progress.index') }}" class="row g-3 bg-light p-4 rounded mb-7 bg-opacity-50">
            <div class="col-md-4">
                <label class="form-label fs-7 fw-bold">Pencarian Member</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama member..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-7 fw-bold">Dari Tgl Booking</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fs-7 fw-bold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-info w-100 flex-grow-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to']))
                    <a href="{{ route('trainer.progress.index') }}" class="btn btn-sm btn-light-danger ms-2 px-3" title="Reset Filters">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Layanan & Member</th>
                        <th class="min-w-125px">Tgl Booking</th>
                        <th class="min-w-100px text-end pe-4">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ substr($trx->user->name ?? 'M', 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bold mb-1 fs-6">{{ $trx->service->name }}</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Member: {{ $trx->user->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fw-semibold d-block fs-7">
                                    {{ \Carbon\Carbon::parse($trx->scheduled_date)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @php
                                    $completedCount = $trx->serviceSessions->where('status', 'attended')->count();
                                    $totalCount = $trx->serviceSessions->count();
                                @endphp
                                <span class="badge badge-light-primary mb-2">{{ $completedCount }} / {{ $totalCount }} Sesi Selesai</span>
                                
                                <div class="d-inline-flex ms-2">
                                    <button class="btn btn-sm btn-icon btn-light-danger w-30px h-30px me-1" type="button" onclick="if(confirm('Yakin ingin menghapus seluruh data booking dan riwayat sesi layanan ini?')) document.getElementById('del-trx-{{ $trx->id }}').submit();" title="Hapus Data Transaksi/Layanan Ini">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="del-trx-{{ $trx->id }}" action="{{ route('trainer.progress.transaction.destroy', $trx->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button class="btn btn-sm btn-icon btn-light btn-active-light-primary w-30px h-30px" type="button" data-bs-toggle="collapse" data-bs-target="#sessions-trx-{{ $trx->id }}" title="Lihat Sesi">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="sessions-trx-{{ $trx->id }}" class="collapse bg-light-secondary">
                            <td colspan="3" class="px-6 py-4">
                                <div class="fw-bold fs-6 mb-3 text-dark">Manajemen Sesi Pertemuan</div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless table-striped align-middle gs-0 gy-2 mb-0">
                                        <thead>
                                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                <th class="w-20px">
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input select-all-sessions" type="checkbox" data-trx="{{ $trx->id }}" />
                                                    </div>
                                                </th>
                                                <th class="w-100px">Sesi</th>
                                                <th class="min-w-200px">Tgl & Jam</th>
                                                <th class="min-w-250px">Topik / Progress</th>
                                                <th class="min-w-150px">Kehadiran</th>
                                                <th class="w-100px text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-semibold fs-7">
                                            @forelse($trx->serviceSessions as $session)
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input session-chk-{{ $trx->id }}" type="checkbox" value="{{ $session->id }}" />
                                                        </div>
                                                    </td>
                                                    <form action="{{ route('trainer.progress.session.update', $session->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <td class="ps-2 align-middle">
                                                            <span class="badge badge-outline badge-dark">Sesi {{ $session->session_number }}</span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="datetime-local" class="form-control form-control-sm form-control-solid" name="scheduled_date" value="{{ $session->scheduled_date ? \Carbon\Carbon::parse($session->scheduled_date)->format('Y-m-d\TH:i') : '' }}" required>
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="text" class="form-control form-control-sm form-control-solid" name="topic" value="{{ $session->topic }}" placeholder="Catat progress atau materi..">
                                                        </td>
                                                        <td class="align-middle">
                                                            <select class="form-select form-select-sm form-select-solid mb-1" name="status">
                                                                <option value="pending" {{ $session->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="attended" {{ $session->status == 'attended' ? 'selected' : '' }}>Hadir (Selesai)</option>
                                                                <option value="missed" {{ $session->status == 'missed' ? 'selected' : '' }}>Tidak Hadir (Kelewat)</option>
                                                                <option value="cancelled" {{ $session->status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                                                            </select>
                                                            @if($session->checkin_id && $session->checkinLog)
                                                                <div class="text-success fs-8 mt-1">
                                                                    <i class="fas fa-check-circle text-success fs-8 me-1"></i> Checked-in: {{ \Carbon\Carbon::parse($session->checkinLog->checkin_time)->format('H:i') }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="text-end align-middle">
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                Simpan
                                                            </button>
                                                        </td>
                                                    </form>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-5">Sesi pertemuan belum digenerate atau paket ini tidak memiliki pembagian sesi terjadwal.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="pt-4 border-top">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted fw-semibold me-4">Aksi Massal:</span>
                                                        <button class="btn btn-sm btn-light-success bulk-action-btn me-2" data-action="attended" data-trx="{{ $trx->id }}">Tandai Selesai</button>
                                                        <button class="btn btn-sm btn-light-warning bulk-action-btn me-2" data-action="missed" data-trx="{{ $trx->id }}">Tandai Kelewat</button>
                                                        <button class="btn btn-sm btn-light-danger bulk-action-btn" data-action="delete" data-trx="{{ $trx->id }}">Hapus Sesi</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-10">Belum ada member atau layanan yang ditugaskan kepada Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!--end::Body-->
</div>

<!-- Hidden Form for Bulk Action -->
<form id="bulkSessionForm" action="{{ route('trainer.progress.session.bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkSessionInputs"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-all-sessions').forEach(function(el) {
        el.addEventListener('change', function() {
            const trxId = this.getAttribute('data-trx');
            const checkboxes = document.querySelectorAll('.session-chk-' + trxId);
            checkboxes.forEach(chk => chk.checked = this.checked);
        });
    });

    document.querySelectorAll('.bulk-action-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const trxId = this.getAttribute('data-trx');
            const checkboxes = document.querySelectorAll('.session-chk-' + trxId + ':checked');

            if (checkboxes.length === 0) {
                alert('Pilih minimal satu sesi untuk melanjutkan aksi massal ini.');
                return;
            }

            let actionText = '';
            if(action === 'attended') actionText = 'Tandai Selesai / Hadir';
            if(action === 'missed') actionText = 'Tandai Kelewat / Tidak Hadir';
            if(action === 'delete') actionText = 'Hapus Permanen';

            if(confirm('Yakin ingin ' + actionText + ' untuk ' + checkboxes.length + ' sesi yang dipilih?')) {
                const bulkForm = document.getElementById('bulkSessionForm');
                const inputsContainer = document.getElementById('bulkSessionInputs');
                
                inputsContainer.innerHTML = '';
                
                checkboxes.forEach(function(chk) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'session_ids[]';
                    input.value = chk.value;
                    inputsContainer.appendChild(input);
                });

                document.getElementById('bulkActionInput').value = action;
                bulkForm.submit();
            }
        });
    });
});
</script>
@endsection