<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
