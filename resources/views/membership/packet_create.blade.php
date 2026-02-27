@extends('layouts.app')

@section('title', 'Tambah Paket Membership')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Tambah Paket Membership</h3>

    <form action="{{ route('packet_membership.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Paket</label>
            <input type="text" class="form-control" id="name" name="name" required maxlength="50" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Harga (Rp)</label>
            <input type="number" class="form-control" id="price" name="price" required min="0" value="{{ old('price') }}">
        </div>
        <div class="mb-3">
            <label for="duration_days" class="form-label">Durasi (hari)</label>
            <input type="number" class="form-control" id="duration_days" name="duration_days" required min="1" value="{{ old('duration_days') }}">
        </div>
        <div class="mb-3">
            <label for="max_visits" class="form-label">Maksimal Kunjungan</label>
            <input type="number" class="form-control" id="max_visits" name="max_visits" required min="1" value="{{ old('max_visits') }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi (opsional)</label>
            <textarea class="form-control" id="description" name="description" maxlength="500">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('packet_membership') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
