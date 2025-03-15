<?php

namespace App\Http\Controllers;

use App\Models\AdditionalInfo;
use Illuminate\Http\Request;

class AdditionalInfoController extends Controller
{
    public function index()
    {
        $info = AdditionalInfo::with('image')->get();
        return response()->json($info, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageId = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('images', 'public');
            $image = \App\Models\Image::create(['path' => $path]);
            $imageId = $image->id;
        }

        $additionalInfo = new AdditionalInfo();
        $additionalInfo->name = $validated['name'];
        $additionalInfo->text = $validated['text'] ?? null;
        $additionalInfo->id_image = $imageId;
        $additionalInfo->save();

        return response()->json($additionalInfo->load('image'), 201);
    }


    public function show($id)
    {
        $info = AdditionalInfo::with('image')->find($id);
        if (!$info) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($info, 200);
    }

    public function update(Request $request, $id)
{
    $info = AdditionalInfo::find($id);
    if (!$info) {
        return response()->json(['message' => 'Not found'], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'text' => 'sometimes|string|nullable',
        'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $info->name = $validated['name'] ?? $info->name;
    $info->text = $validated['text'] ?? $info->text;

    if ($request->hasFile('image_file')) {
        $file = $request->file('image_file');
        $path = $file->store('images', 'public');
        $newImage = \App\Models\Image::create(['path' => $path]);

        $oldImageId = $info->id_image;

        $info->id_image = $newImage->id;

        $info->save();

        if ($oldImageId) {
            $oldImage = \App\Models\Image::find($oldImageId);
            if ($oldImage) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
        }
    } else {
        $info->save();
    }

    return response()->json($info->load('image'), 200);
}



    public function destroy($id)
    {
        $info = AdditionalInfo::find($id);
        if (!$info) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $info->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
