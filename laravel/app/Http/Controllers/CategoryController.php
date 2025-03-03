<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
    return response()->json(Category::with('image'));
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
        ]);

        // Vytvorenie kategórie s priradeným obrázkom
        Category::create([
            'name' => $validated['name'],
            'id_image' => $image->id,
        ]);

        return redirect()->back()->with('success', 'Kategória bola pridaná!');
    }
    public function edit($id)
{
    $category = Category::findOrFail($id);
    return view('categories.edit', compact('category'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $category = Category::findOrFail($id);

    if ($request->hasFile('image')) {
        if ($category->image) {
            \Storage::disk('public')->delete($category->image->path);
            $category->image->delete();
        }

        $path = $request->file('image')->store('images', 'public');
        $image = Image::create([
            'path' => $path,
        ]);
        $category->id_image = $image->id;
    }

    $category->name = $validated['name'];
    $category->save();

    return redirect('/admin')->with('success', 'Kategória bola aktualizovaná!');
}


public function destroy($id)
{
    $category = Category::findOrFail($id);
    $image = $category->image; 

    
    $category->delete();

    if ($image) {
        \Storage::disk('public')->delete($image->path);

        $image->delete();
    }

    return redirect()->route('categories.index')->with('success', 'Kategória bola vymazaná!');
}


}
