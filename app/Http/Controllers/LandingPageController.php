<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    // Berita CRUD
    public function index()
    {
        $beritas = Berita::all();
        $galleries = Gallery::all();
        return view('landing_page.index', compact('beritas', 'galleries'));
    }

    // Public landing page view
    public function publicLandingPage()
    {
        $beritas = Berita::orderBy('start_date', 'desc')->get();
        $galleries = Gallery::all();
        return view('landing_page.public', compact('beritas', 'galleries'));
    }

    public function createBerita()
    {
        return view('landing_page.create_berita');
    }

    public function storeBerita(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('berita_images', 'public');
            $validated['image'] = $path;
        }

        Berita::create($validated);

        return redirect()->route('landing_page.index')->with('success', 'Berita created successfully.');
    }

    public function editBerita($id)
    {
        $berita = Berita::findOrFail($id);
        return view('landing_page.edit_berita', compact('berita'));
    }

    public function updateBerita(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($berita->image) {
                Storage::disk('public')->delete($berita->image);
            }
            $path = $request->file('image')->store('berita_images', 'public');
            $validated['image'] = $path;
        }

        $berita->update($validated);

        return redirect()->route('landing_page.index')->with('success', 'Berita updated successfully.');
    }

    public function destroyBerita($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->image) {
            Storage::disk('public')->delete($berita->image);
        }
        $berita->delete();

        return redirect()->route('landing_page.index')->with('success', 'Berita deleted successfully.');
    }

    // Gallery CRUD
    public function createGallery()
    {
        return view('landing_page.create_gallery');
    }

    public function storeGallery(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('gallery_images', 'public');
        $validated['image'] = $path;

        Gallery::create($validated);

        return redirect()->route('landing_page.index')->with('success', 'Gallery item created successfully.');
    }

    public function editGallery($id)
    {
        $gallery = Gallery::findOrFail($id);
        return view('landing_page.edit_gallery', compact('gallery'));
    }

    public function updateGallery(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            $path = $request->file('image')->store('gallery_images', 'public');
            $validated['image'] = $path;
        }

        $gallery->update($validated);

        return redirect()->route('landing_page.index')->with('success', 'Gallery item updated successfully.');
    }

    public function destroyGallery($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();

        return redirect()->route('landing_page.index')->with('success', 'Gallery item deleted successfully.');
    }
}
