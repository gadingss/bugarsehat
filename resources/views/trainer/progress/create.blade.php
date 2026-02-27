@extends('layouts.app')

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ $config['title'] }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $config['title-alias'] }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('trainer.progress.index') }}" class="btn btn-sm btn-light">
                    Kembali
                </a>
            </div>
        </div>

        <!--begin::Body-->
        <div class="card-body py-3">
            <form action="{{ route('trainer.progress.store') }}" method="POST">
                @csrf

                <div class="mb-10">
                    <label class="form-label required">Pilih Member</label>
                    <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Member --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-10">
                    <label class="form-label required">Tanggal Progress</label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                        value="{{ date('Y-m-d') }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-10">
                    <label class="form-label required">Catatan Progress (Hasil Latihan)</label>
                    <textarea name="progress_note" class="form-control @error('progress_note') is-invalid @enderror"
                        rows="4" required placeholder="Masukkan pencapaian latihan hari ini..."></textarea>
                    @error('progress_note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-10">
                    <label class="form-label">Catatan Khusus (Jika ada peringatan/cedera dll)</label>
                    <textarea name="special_note" class="form-control" rows="3"
                        placeholder="Contoh: Otot paha sedikit tertarik, latihan kaki dikurangi besok."></textarea>
                </div>

                <div class="mb-10">
                    <label class="form-label">Rekomendasi Latihan Selanjutnya</label>
                    <textarea name="recommendation" class="form-control" rows="3"
                        placeholder="Saran pola makan atau menu latihan berikutnya..."></textarea>
                </div>

                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-primary">Simpan Progress</button>
                </div>
            </form>
        </div>
        <!--end::Body-->
    </div>
@endsection