<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Season;
use App\Models\Image;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        return view('admin_panel', [
            'seasons' => Season::with('image')->get(),
            'categories' => Category::with('image')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Uloženie obrázka
        $path = $request->file('image')->store('images', 'public');
        $image = Image::create([
            'path' => $path,
            'id_gallery' => null, // Nie je súčasťou galérie
        ]);

        // Vytvorenie sezóny s priradeným obrázkom
        Season::create([
            'name' => $validated['name'],
            'id_image' => $image->id,
        ]);

        return redirect()->back()->with('success', 'Sezóna bola pridaná!');
    }

    public function edit($id)
{
    $season = Season::findOrFail($id);
    return view('seasons.edit', compact('season'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $season = Season::findOrFail($id);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images', 'public');
        $image = Image::create([
            'path' => $path,
            'id_gallery' => null,
        ]);
        $season->id_image = $image->id;
    }

    $season->name = $validated['name'];
    $season->save();

    return redirect('/admin')->with('success', 'Sezona bola aktualizovaná!');
}

public function destroy($id)
{
    $season = Season::findOrFail($id);
    $image = $season->image; 

    
    $season->delete();

    if ($image) {
        \Storage::disk('public')->delete($image->path);

        $image->delete();
    }

    return redirect()->route('seasons.index')->with('success', 'Sezona bola vymazaná!');
}

}
