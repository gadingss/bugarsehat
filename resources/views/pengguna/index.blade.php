@extends('layouts.app')

@section('title', $config['title-alias'] ?? 'Daftar Pengguna')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">👥 {{ $config['title-alias'] ?? 'Daftar Pengguna' }}</h4>
            <a href="{{ route('pengguna.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus me-1"></i> Tambah Pengguna
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($pengguna->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>Tidak ada data pengguna (Owner/Staff).
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="width: 15%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengguna as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong><br>
                                    <small class="text-muted">{{ $user->phone ?? 'No HP tidak tersedia' }}</small>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $spatie = $user->getRoleNames()->first();
                                        $display = $spatie ?? ucfirst($user->role ?? 'member');
                                    @endphp
                                    @if(str_contains($display, 'Owner'))
                                        <span class="badge bg-dark">Owner</span>
                                    @elseif(str_contains($display, 'Staff'))
                                        <span class="badge bg-info">Staff</span>
                                    @elseif(str_contains($display, 'Trainer'))
                                        <span class="badge bg-success">Trainer</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($display) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('pengguna.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna \'{{ $user->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
