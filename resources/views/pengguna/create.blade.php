@extends('layouts.app')

@section('title', $config['title'])

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Tambah Pengguna Baru</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('pengguna.store') }}">
                @csrf
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="User:Owner">Owner</option>
                                        <option value="User:Staff">Staff</option>
                                        <option value="User:Trainer">Trainer</option>
                                    </select>
                                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">No. HP (Opsional)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('pengguna') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
