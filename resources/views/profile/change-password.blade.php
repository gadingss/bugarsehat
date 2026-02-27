@extends('layouts.app')

@section('title', $config['title'])

@section('content')
<div class="container py-4">
    <h2>{{ $config['title-alias'] }}</h2>

    <form method="POST" action="{{ route('profile.update-password') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="current_password" class="form-label">Password Lama</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Ubah Password</button>
            <a href="{{ route('profile') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
