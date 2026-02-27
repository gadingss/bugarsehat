@extends('layouts.app')

@section('title', 'Status Membership')

@section('content')
<div class="container py-4">
    <h2>Status Membership</h2>

    <p><strong>Nama:</strong> {{ $user->name }}</p>

    @if($activeMembership)
        <p><strong>Paket Aktif:</strong> {{ $activeMembership->package->name }}</p>
        <p><strong>Tanggal Mulai:</strong> {{ $activeMembership->start_date->format('d M Y') }}</p>
        <p><strong>Tanggal Berakhir:</strong> {{ $activeMembership->end_date->format('d M Y') }}</p>
        <p><strong>Kunjungan Tersisa:</strong>
            @if($activeMembership->remaining_visits == 999)
                Unlimited
            @else
                {{ $activeMembership->remaining_visits }} kali
            @endif
        </p>
    @else
        <div class="alert alert-warning">Tidak ada membership aktif.</div>
    @endif

    <h4>Riwayat Membership</h4>
    <ul>
        @foreach($allMemberships as $item)
            <li>{{ $item->package->name }} ({{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }})</li>
        @endforeach
    </ul>

    <a href="{{ route('profile') }}" class="btn btn-secondary mt-3">Kembali ke Profil</a>
</div>
@endsection
