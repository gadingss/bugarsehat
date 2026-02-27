@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Landing Page Management</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2>Berita / Event / Promo</h2>
    <a href="{{ route('landing_page.berita.create') }}" class="btn btn-primary mb-3">Add New Berita</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beritas as $berita)
            <tr>
                <td>{{ $berita->title }}</td>
                <td>{{ $berita->type }}</td>
                <td>{{ $berita->start_date }}</td>
                <td>{{ $berita->end_date }}</td>
                <td>
                    @if($berita->image)
                        <img src="{{ asset('storage/' . $berita->image) }}" alt="{{ $berita->title }}" width="100">
                    @endif
                </td>
                <td>
                    <a href="{{ route('landing_page.berita.edit', $berita->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('landing_page.berita.destroy', $berita->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this berita?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Gallery</h2>
    <a href="{{ route('landing_page.gallery.create') }}" class="btn btn-primary mb-3">Add New Gallery Item</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($galleries as $gallery)
            <tr>
                <td>{{ $gallery->title }}</td>
                <td>{{ $gallery->description }}</td>
                <td>
                    @if($gallery->image)
                        <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" width="100">
                    @endif
                </td>
                <td>
                    <a href="{{ route('landing_page.gallery.edit', $gallery->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('landing_page.gallery.destroy', $gallery->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this gallery item?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
