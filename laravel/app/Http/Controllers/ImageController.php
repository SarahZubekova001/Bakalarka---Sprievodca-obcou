<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        
        $path = $request->file('image')->store('images', 'public');

        
        $image = Image::create([
            'path' => $path,
            'id_gallery' => null,
        ]);

        return response()->json(['id' => $image->id, 'path' => $image->path], 201);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        $events = \App\Models\Event::where('id_image', $image->id)->get();
        foreach ($events as $ev) {
            $ev->id_image = null;
            $ev->save();
        }

        \Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Obrázok bol odstránený!'], 200);
    }


}
