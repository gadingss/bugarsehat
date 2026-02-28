@extends('layouts.app')

@section('title', $config['title-alias'] ?? 'Edit Pengguna')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark">📝 {{ $config['title-alias'] }}</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($pengguna) && $pengguna->id)
                            <form action="{{ route('pengguna.update', $pengguna) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Nama --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $pengguna->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Alamat Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $pengguna->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Telepon --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone', $pengguna->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Role --}}
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                        required>
                                        <option value="User:Staff" {{ old('role', $pengguna->getRoleNames()->first() ?? ucfirst($pengguna->role)) == 'User:Staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="User:Owner" {{ old('role', $pengguna->getRoleNames()->first() ?? ucfirst($pengguna->role)) == 'User:Owner' ? 'selected' : '' }}>Owner</option>
                                        <option value="User:Trainer" {{ old('role', $pengguna->getRoleNames()->first() ?? ucfirst($pengguna->role)) == 'User:Trainer' ? 'selected' : '' }}>Trainer</option>
                                        <option value="User:Member" {{ old('role', $pengguna->getRoleNames()->first() ?? ucfirst($pengguna->role)) == 'User:Member' ? 'selected' : '' }}>Member</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('pengguna.index') }}" class="btn btn-secondary me-2">Batal</a>
                                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-danger">
                                Data pengguna tidak ditemukan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection