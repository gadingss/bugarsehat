@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Berita / Event / Promo</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('landing_page.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $berita->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="berita" {{ old('type', $berita->type) == 'berita' ? 'selected' : '' }}>Berita</option>
                <option value="event" {{ old('type', $berita->type) == 'event' ? 'selected' : '' }}>Event</option>
                <option value="promo" {{ old('type', $berita->type) == 'promo' ? 'selected' : '' }}>Promo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $berita->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $berita->start_date) }}">
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $berita->end_date) }}">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image (optional)</label>
            @if($berita->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $berita->image) }}" alt="{{ $berita->title }}" width="150">
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('landing_page.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
