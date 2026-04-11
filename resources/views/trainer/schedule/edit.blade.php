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
        <form action="{{ route('trainer.schedule.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="title" class="form-label required">Judul Kelas</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $schedule->title) }}" placeholder="Contoh: Yoga Pagi" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang kelas...">{{ old('description', $schedule->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label required">Waktu Mulai</label>
                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time->format('Y-m-d\\TH:i')) }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_time" class="form-label required">Waktu Selesai</label>
                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time->format('Y-m-d\\TH:i')) }}" required>
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="service_id" class="form-label required">Layanan Kelas</label>
                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                    <option value="">Pilih Layanan</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" data-sessions="{{ json_encode($service->sessionTemplates) }}" data-max-sessions="{{ $service->sessions_count }}" {{ old('service_id', $schedule->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} ({{ $service->sessions_count }} Sesi)
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="session_container" style="display: none;">
                <label for="session_number" class="form-label required">Topik / Sesi Ke-</label>
                <select class="form-select @error('session_number') is-invalid @enderror" id="session_number" name="session_number">
                    <option value="">Pilih Sesi</option>
                </select>
                @error('session_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label required">Kapasitas Peserta</label>
                <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $schedule->capacity) }}" min="1" required>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('trainer.schedule.index') }}" class="btn btn-light me-3">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function populateSessions() {
            var selectedOpt = $('#service_id').find(':selected');
            if(selectedOpt.val()) {
                var templates = selectedOpt.data('sessions');
                var maxSessions = selectedOpt.data('max-sessions');
                
                var oldSess = "{{ old('session_number', $schedule->session_number) }}";
                $('#session_number').empty().append('<option value="">Pilih Sesi</option>');
                
                for(var i = 1; i <= maxSessions; i++) {
                    var topicStr = "";
                    if (templates && templates.length > 0) {
                        var t = templates.find(item => item.session_number == i);
                        if(t && t.topic) {
                            topicStr = " - " + t.topic;
                        }
                    }
                    var selected = (oldSess == i) ? 'selected' : '';
                    $('#session_number').append('<option value="'+i+'" '+selected+'>Sesi '+i+topicStr+'</option>');
                }
                
                $('#session_container').slideDown();
            } else {
                $('#session_container').slideUp();
                $('#session_number').empty();
            }
        }
        
        $('#service_id').on('change', function() {
            populateSessions();
            
            // Auto fill title when service change manually
            var selectedOpt = $(this).find(':selected');
            if(selectedOpt.val()) {
                var autoTitle = selectedOpt.text().split('(')[0].trim() + " - Sesi " + ($('#session_number').val() || "...");
                if (!$('#title').val() || $('#title').data('auto')) {
                    $('#title').val(autoTitle).data('auto', true);
                }
            }
        });
        
        $('#session_number').on('change', function() {
            var selectedOpt = $('#service_id').find(':selected');
            if(selectedOpt.val() && $(this).val()) {
                var autoTitle = selectedOpt.text().split('(')[0].trim() + " - Sesi " + $(this).val();
                if (!$('#title').val() || $('#title').data('auto')) {
                    $('#title').val(autoTitle).data('auto', true);
                }
            }
        });
        
        $('#title').on('input', function() {
            $(this).data('auto', false);
        });

        populateSessions(); // On load
    });
</script>
@endsection
