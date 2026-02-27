@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Gallery Item</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('landing_page.gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $gallery->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (optional)</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $gallery->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            @if($gallery->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" width="150">
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('landing_page.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
