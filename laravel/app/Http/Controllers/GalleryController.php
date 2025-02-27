<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Image;
use App\Models\Post;
use App\Models\Restaurant;
use App\Models\Hiking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('images')->get();
        return view('galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('galleries.create', [
            'posts' => Post::all(),
            'restaurants' => Restaurant::all(),
            'hikings' => Hiking::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_post' => 'nullable|exists:post,id',
            'id_restaurant' => 'nullable|exists:restaurant,id',
            'id_hiking' => 'nullable|exists:hiking,id',
        ]);

        $gallery = Gallery::create($validated);
        return redirect()->route('galleries.index')->with('success', 'Galéria bola vytvorená!');
    }

    public function show(Gallery $gallery)
    {
        return view('galleries.show', compact('gallery'));
    }

    public function uploadImage(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('images', 'public');

        Image::create([
            'id_gallery' => $gallery->id,
            'path' => $path,
        ]);

        return redirect()->route('galleries.show', $gallery->id)->with('success', 'Obrázok bol pridaný!');
    }

    public function deleteImage(Image $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->back()->with('success', 'Obrázok bol odstránený!');
    }

    public function destroy(Gallery $gallery)
    {
        foreach ($gallery->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $gallery->delete();

        return redirect()->route('galleries.index')->with('success', 'Galéria bola vymazaná!');
    }
}
