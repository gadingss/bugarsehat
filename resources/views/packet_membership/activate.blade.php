@extends('layouts.app')

@section('title', 'Aktivasi Membership')

@section('content')
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0">Aktivasi Membership</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3">
                <p><strong>Nama Paket:</strong> {{ $membership->package->name }}</p>
                <p><strong>Harga:</strong> Rp {{ number_format($membership->package->price, 0, ',', '.') }}</p>
                <p><strong>Durasi:</strong> {{ $membership->package->duration_days }} hari</p>
                <p><strong>Status Saat Ini:</strong>
                    @if ($membership->status === 'active')
                        <span class="badge bg-success">Aktif</span>
                    @elseif ($membership->status === 'pending')
                        <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                    @else
                        <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </p>
            </div>

            @if ($membership->status !== 'active')
                <form action="{{ route('packet_membership.activate', $membership->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Aktifkan Membership</button>
                </form>
            @else
                <div class="alert alert-info mt-3">Membership ini sudah aktif.</div>
            @endif
        </div>
    </div>
</div>
@endsection
