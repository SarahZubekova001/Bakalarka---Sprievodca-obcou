<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Season;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeasonController extends Controller
{

    public function index()
    {
        return response()->json(Season::with('image')->get(), 200, [], JSON_PRETTY_PRINT);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('images', 'public');
        $image = Image::create([
            'path' => $path,
            'id_gallery' => null,
        ]);

        $season = Season::create([
            'name' => $validated['name'],
            'id_image' => $image->id,
        ]);

        return response()->json($season, 201);
    }

    public function show($id)
    {
        $season = Season::with('image')->findOrFail($id);
        return response()->json($season);
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
            $newImage = Image::create([
                'path' => $path,
                'id_gallery' => null,
            ]);

            // 🔹 Najprv aktualizuj id_image, aby odkazoval na nový obrázok
            $oldImage = $season->image;
            $season->id_image = $newImage->id;
            $season->save();

            // 🔹 Potom vymaž starý obrázok (ak existuje)
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
        }

        $season->name = $validated['name'];
        $season->save();

        return response()->json($season);
    }


    public function destroy($id)
    {
        $season = Season::findOrFail($id);
        $image = $season->image; 

        $season->delete();

        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        return response()->json(['message' => 'Sezóna bola vymazaná'], 200);
    }
}
