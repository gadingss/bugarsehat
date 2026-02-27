@extends('layouts.app')

@section('title', 'Daftar Paket Membership')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Paket Membership</h3>

    <a href="{{ route('packet_membership.create') }}" class="btn btn-primary mb-3">Tambah Paket</a>

    <div class="row">
        @foreach($packets as $packet)
            <div class="col-md-4 mb-3">
                <div class="card p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar bg-success text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($packet->name, 0, 1)) }}
                        </div>
                        <h5 class="mb-0">{{ $packet->name }}</h5>
                    </div>
                    <h3 class="text-primary">Rp {{ number_format($packet->price, 0, ',', '.') }}</h3>
                    <p class="mb-1">{{ $packet->description }}</p>
                    <ul class="list-unstyled mb-0">
                        <li>Durasi: {{ $packet->duration_days }} hari</li>
                        <li>Maksimal kunjungan: {{ $packet->max_visits }} kali</li>
                        <li>Akses semua fasilitas</li>
                    </ul>
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="#" class="btn btn-light">Lihat Detail</a>
                        <a href="#" class="btn btn-success">Pilih Paket</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
