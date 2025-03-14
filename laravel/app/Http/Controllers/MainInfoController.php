<?php

namespace App\Http\Controllers;

use App\Models\MainInfo;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Http\Request;

class MainInfoController extends Controller
{
    public function showSingle()
    {
        $info = MainInfo::with('logoImage', 'gallery.images')->find(1);

        if (!$info) {
            return response()->json([
                'id'         => null,
                'town_name'  => '',
                'description'=> '',
                'logo'       => null,
                'logoImage'  => null,
                'gallery'    => null,
            ], 200);
        }

        return response()->json($info, 200);
    }


    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'town_name'   => 'required|string|max:255',
            'description' => 'nullable|string',
            'gallery_files'   => 'nullable|array',
            'gallery_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoImageId = null;
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $path = $file->store('logos', 'public');
            $logoImage = Image::create([
                'path' => $path
            ]);
            $logoImageId = $logoImage->id;
        }

        $info = MainInfo::find(1);
        if (!$info) {
            $info = MainInfo::create(array_merge($validated, ['id' => 1]));
        } else {
            $info->update($validated);
        }

        if ($logoImageId) {
            $info->logo = $logoImageId;
            $info->save();
        }

        $gallery = Gallery::firstOrCreate(
            ['id_main_info' => $info->id],
            [
                'id_post' => null,
                'id_restaurant' => null,
                'id_hiking' => null,
            ]
        );

        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $galleryFile) {
                $gPath = $galleryFile->store('maininfo-gallery', 'public');
                Image::create([
                    'id_gallery' => $gallery->id,
                    'path' => $gPath,
                ]);
            }
        }

        return response()->json($info->load('logoImage', 'gallery.images'), 200);
    }


}
