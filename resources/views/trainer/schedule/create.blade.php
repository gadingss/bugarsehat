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
            <a href="{{ route('trainer.schedule.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div class="card-body py-3">
        <form action="{{ route('trainer.schedule.store') }}" method="POST" id="schedule-form">
            @csrf

            {{-- Pilih Layanan --}}
            <div class="mb-5">
                <label for="service_id" class="form-label required fw-semibold">Pilih Layanan</label>
                <select class="form-select form-select-solid @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}"
                            data-sessions="{{ json_encode($service->sessionTemplates) }}"
                            data-max-sessions="{{ $service->sessions_count }}"
                            data-name="{{ $service->name }}"
                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} ({{ $service->sessions_count }} Sesi)
                        </option>
                    @endforeach
                </select>
                @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Kapasitas --}}
            <div class="mb-5">
                <label for="capacity" class="form-label required fw-semibold">Kapasitas Peserta per Sesi</label>
                <input type="number" class="form-control form-control-solid @error('capacity') is-invalid @enderror"
                    id="capacity" name="capacity" value="{{ old('capacity', 10) }}" min="1" required>
                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Dynamic Sessions Container --}}
            <div id="sessions-container" style="display:none;">
                <div class="separator separator-dashed my-5"></div>
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-calendar-alt text-primary me-2 fs-4"></i>
                    <h4 class="fw-bold mb-0 text-gray-800">Atur Jadwal per Sesi</h4>
                </div>
                <div id="sessions-list"></div>
            </div>

            <div class="d-flex justify-content-end mt-5">
                <button type="reset" class="btn btn-light me-3" onclick="$('#sessions-container').hide(); $('#sessions-list').empty();">Reset</button>
                <button type="submit" class="btn btn-primary" id="submit-btn" style="display:none;">
                    <i class="fas fa-save me-2"></i>Simpan Semua Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    function buildSessionRows(serviceOpt) {
        var maxSessions = parseInt(serviceOpt.data('max-sessions')) || 1;
        var templates   = serviceOpt.data('sessions') || [];
        var serviceName = serviceOpt.data('name') || serviceOpt.text().split('(')[0].trim();
        var html = '';

        for (var i = 1; i <= maxSessions; i++) {
            var topic = 'Sesi ' + i;
            if (templates && templates.length > 0) {
                var t = templates.find(item => item.session_number == i);
                if (t && t.topic) topic = t.topic;
            }

            html += `
            <div class="card border border-dashed border-gray-300 mb-4 p-0">
                <div class="card-header min-h-50px bg-light-primary border-0">
                    <div class="card-title">
                        <span class="badge badge-primary me-2">${i}</span>
                        <span class="fw-bold text-dark">${serviceName} — ${topic}</span>
                    </div>
                </div>
                <div class="card-body py-4">
                    <input type="hidden" name="sessions[${i}][session_number]" value="${i}">
                    <input type="hidden" name="sessions[${i}][title]" value="${serviceName} - ${topic}">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal &amp; Jam Mulai</label>
                            <input type="datetime-local" class="form-control form-control-solid"
                                name="sessions[${i}][start_time]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal &amp; Jam Selesai</label>
                            <input type="datetime-local" class="form-control form-control-solid"
                                name="sessions[${i}][end_time]" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi Sesi <span class="text-muted fs-8">(opsional)</span></label>
                            <input type="text" class="form-control form-control-solid"
                                name="sessions[${i}][description]" placeholder="Contoh: Fokus pada pemanasan dan teknik dasar">
                        </div>
                    </div>
                </div>
            </div>`;
        }

        return html;
    }

    $(document).ready(function() {
        $('#service_id').on('change', function() {
            var selected = $(this).find(':selected');
            if (selected.val()) {
                $('#sessions-list').html(buildSessionRows(selected));
                $('#sessions-container').slideDown();
                $('#submit-btn').show();
            } else {
                $('#sessions-container').slideUp();
                $('#submit-btn').hide();
                $('#sessions-list').empty();
            }
        });

        // Auto-set end_time when start_time is filled (+ 1 hour)
        $(document).on('change', 'input[name*="[start_time]"]', function() {
            var sessionDiv = $(this).closest('.row');
            var endInput = sessionDiv.find('input[name*="[end_time]"]');
            if ($(this).val() && !endInput.val()) {
                var start = new Date($(this).val());
                start.setHours(start.getHours() + 1);
                var pad = n => String(n).padStart(2,'0');
                var formatted = start.getFullYear() + '-' + pad(start.getMonth()+1) + '-' + pad(start.getDate())
                    + 'T' + pad(start.getHours()) + ':' + pad(start.getMinutes());
                endInput.val(formatted);
            }
        });

        // Load on back/old value
        @if(old('service_id'))
            $('#service_id').trigger('change');
        @endif
    });
</script>
@endsection
