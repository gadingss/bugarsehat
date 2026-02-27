@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Berita / Event / Promo</h1>
    <div class="row">
        @foreach($beritas as $berita)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if($berita->image)
                <img src="{{ asset('storage/' . $berita->image) }}" class="card-img-top" alt="{{ $berita->title }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $berita->title }}</h5>
                    <p class="card-text">{{ Str::limit($berita->content, 100) }}</p>
                    <p class="card-text"><small class="text-muted">{{ ucfirst($berita->type) }}</small></p>
                    @if($berita->start_date)
                    <p class="card-text">Mulai: {{ $berita->start_date }}</p>
                    @endif
                    @if($berita->end_date)
                    <p class="card-text">Berakhir: {{ $berita->end_date }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <h1>Gallery</h1>
    <div class="row">
        @foreach($galleries as $gallery)
        <div class="col-md-3 mb-4">
            <div class="card">
                @if($gallery->image)
                <img src="{{ asset('storage/' . $gallery->image) }}" class="card-img-top" alt="{{ $gallery->title }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $gallery->title }}</h5>
                    <p class="card-text">{{ $gallery->description }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
