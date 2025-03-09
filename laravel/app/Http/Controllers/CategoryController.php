<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('image')->get()); // Doplnené ->get()
    }

    public function show($id)
    {
        $category = Category::with('image')->findOrFail($id);
        return response()->json($category);
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('images', 'public');
        $image = Image::create(['path' => $path]);

        $category = Category::create([
            'name' => $validated['name'],
            'id_image' => $image->id,
        ]);

        return response()->json([
            'message' => 'Kategória bola pridaná!',
            'category' => $category
        ], 201); 
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
            $path = $request->file('image')->store('images', 'public');
            $newImage = Image::create([
                'path' => $path,
                'id_gallery' => null,
            ]);

            
            $oldImage = $category->image;
            $category->id_image = $newImage->id;
            $category->save();

            
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
        }

        $category->name = $validated['name'];
        $category->save();

        return response()->json($category);
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
