<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Season;
use App\Models\Address;
use App\Models\Town;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
{
    return response()->json(Post::with(['category', 'season', 'address', 'gallery.images'])->get(), 200, [], JSON_PRETTY_PRINT);
}


    public function show($id)
    {
        return response()->json(Post::with(['category', 'season', 'address', 'gallery.images'])->findOrFail($id));
    }

    public function create()
    {
        return view('posts.create', [
            'categories' => Category::all(),
            'seasons' => Season::all(),
            'addresses' => Address::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'postal_code' => 'required|string|max:10',
            'town' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'descriptive_number' => 'required|string|max:10',
            'opening_hours' => 'nullable|string',
            'id_season' => 'nullable|exists:season,id',
            'id_category' => 'required|exists:category,id',
            'url_address' => 'nullable|string|max:255',
            'images' => 'required|array', 
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $town = Town::firstOrCreate(
            ['postal_code' => $validated['postal_code']],
            ['name' => $validated['town']]
        );

        $address = Address::firstOrCreate([
            'street' => $validated['street'],
            'descriptive_number' => $validated['descriptive_number'],
            'postal_code' => $validated['postal_code'],
        ]);

        $post = Post::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'id_address' => $address->id,
            'opening_hours' => $validated['opening_hours'],
            'id_season' => $validated['id_season'],
            'id_category' => $validated['id_category'],
            'url_address' => $validated['url_address'],
        ]);

    
        $gallery = Gallery::firstOrCreate(['id_post' => $post->id]);
        if (!$gallery->id) {
            dd('Chyba: Galéria nebola správne vytvorená', $gallery);
        }

        foreach ($request->file('images') as $imageFile) {
            $path = $imageFile->store('images', 'public');
        
            $image = Image::create([
                'id_gallery' => $gallery->id,
                'path' => $path,
            ]);
        }
        

        return redirect()->route('posts.index')->with('success', 'Príspevok bol vytvorený!');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', [
            'post' => $post,
            'categories' => Category::all(),
            'seasons' => Season::all(),
            'addresses' => Address::all(),
            'images' => $post->gallery ? $post->gallery->images : [],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'postal_code' => 'required|string|max:10',
            'town' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'descriptive_number' => 'required|string|max:10',
            'opening_hours' => 'nullable|string',
            'id_season' => 'nullable|exists:season,id',
            'id_category' => 'required|exists:category,id',
            'url_address' => 'nullable|string|max:255',
            'images' => 'nullable|array', 
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $address = Address::findOrFail($post->id_address);
        $address->update([
            'street' => $validated['street'],
            'descriptive_number' => $validated['descriptive_number'],
            'postal_code' => $validated['postal_code'],
        ]);

        $post->update($validated);

        if (!$post->gallery) {
            $gallery = Gallery::create(['id_post' => $post->id]);
        } else {
            $gallery = $post->gallery;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('images', 'public');
                Image::create([
                    'id_gallery' => $gallery->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Príspevok bol aktualizovaný!');
    }

    public function destroy(Post $post)
    {
        if ($post->gallery) {
            foreach ($post->gallery->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            $post->gallery->delete();
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Príspevok bol vymazaný!');
    }
}
