@extends('layouts.app')

@section('title', $config['title'])

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $config['title'] }}</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <p>{{ $config['description'] }}</p>

            <form method="POST" action="{{ route('configuration.payment.update') }}">
                @csrf

                <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                    <input type="text" class="form-control" name="metode_pembayaran" id="metode_pembayaran" required>
                </div>

                <div class="mb-3">
                    <label for="rekening" class="form-label">Nomor Rekening / E-Wallet</label>
                    <input type="text" class="form-control" name="rekening" id="rekening" required>
                </div>

                <div class="mb-3">
                    <label for="atas_nama" class="form-label">Atas Nama</label>
                    <input type="text" class="form-control" name="atas_nama" id="atas_nama" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan Konfigurasi</button>
            </form>
        </div>
    </div>
</div>
<input type="text" name="metode_pembayaran" class="form-control"
       value="{{ old('metode_pembayaran', $payment->metode_pembayaran ?? '') }}">

<input type="text" name="rekening" class="form-control"
       value="{{ old('rekening', $payment->rekening ?? '') }}">

<input type="text" name="atas_nama" class="form-control"
       value="{{ old('atas_nama', $payment->atas_nama ?? '') }}">

@endsection
