<?php

namespace App\Http\Controllers;

use App\Models\Hiking;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\Season;
use Illuminate\Http\Request;

class HikingController extends Controller
{
    public function index()
    {
        $hikings = Hiking::with(['category', 'season'])->get();
        return view('hiking.index', compact('hikings'));
    }

    public function create()
    {
        return view('hiking.create', [
            'categories' => Category::all(),
            'seasons' => Season::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'length' => 'nullable|integer|min:1',
            'hours' => 'nullable|integer|min:0|max:23',
            'minutes' => 'nullable|integer|min:0|max:59',
            'difficulty' => 'nullable|integer|min:1|max:5',
            'id_category' => 'required|exists:category,id',
            'id_season' => 'required|exists:season,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hiking = Hiking::create($validated);

        // Vytvorenie galérie
        $gallery = Gallery::firstOrCreate(['id_hiking' => $hiking->id]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('images', 'public');
                Image::create([
                    'id_gallery' => $gallery->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('hiking.index')->with('success', 'Turistická trasa bola pridaná!');
    }


    public function show(Hiking $hiking)
    {
        return view('hiking.show', compact('hiking'));
    }

    public function edit(Hiking $hiking)
    {
        return view('hiking.edit', [
            'hiking' => $hiking,
            'categories' => Category::all(),
            'seasons' => Season::all(),
        ]);
    }

    public function update(Request $request, Hiking $hiking)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'length' => 'nullable|integer|min:1',
            'hours' => 'nullable|integer|min:0|max:23',
            'minutes' => 'nullable|integer|min:0|max:59',
            'difficulty' => 'nullable|integer|min:1|max:5',
            'id_category' => 'required|exists:category,id',
            'id_season' => 'required|exists:season,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hiking->update($validated);

        if (!$hiking->gallery) {
            $gallery = Gallery::create(['id_hiking' => $hiking->id]);
        } else {
            $gallery = $hiking->gallery;
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

        return redirect()->route('hiking.index')->with('success', 'Turistická trasa bola aktualizovaná!');
    }


    public function destroy(Hiking $hiking)
    {
        $hiking->delete();
        return redirect()->route('hiking.index')->with('success', 'Turistická trasa bola vymazaná!');
    }
}
