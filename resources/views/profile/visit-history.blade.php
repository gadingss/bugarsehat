@extends('layouts.app')

@section('title', 'Riwayat Kunjungan')

@section('content')
<div class="container py-4">
    <h2>Riwayat Kunjungan</h2>

    @if(isset($visits) && count($visits) > 0)
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Jenis Layanan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $visit)
                    <tr>
                        <td>{{ $visit->tanggal }}</td>
                        <td>{{ $visit->jam }}</td>
                        <td>{{ $visit->layanan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted mt-3">Belum ada riwayat kunjungan.</p>
    @endif

    <a href="{{ route('profile') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
