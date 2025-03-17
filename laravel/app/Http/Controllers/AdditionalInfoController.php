<?php

namespace App\Http\Controllers;

use App\Models\AdditionalInfo;
use App\Models\Address;
use App\Models\Image;
use App\Models\Town;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AdditionalInfoController extends Controller
{
    public function index()
    {
        $info = AdditionalInfo::with('image', 'address.town')->get();
        return response()->json($info, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'nullable|string',
            'town' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'descriptive_number' => 'required|string|max:20',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageId = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('images', 'public');
            $image = Image::create(['path' => $path]);
            $imageId = $image->id;
        }

        $town = Town::firstOrCreate(
            ['postal_code' => $validated['postal_code']],
            ['name' => $validated['town']]
        );
        $address = Address::create([
            'postal_code'        => $town->postal_code, 
            'street'             => $validated['street'],
            'descriptive_number' => $validated['descriptive_number'],
        ]);

        $additionalInfo = new AdditionalInfo();
        $additionalInfo->name = $validated['name'];
        $additionalInfo->text = $validated['text'] ?? null;
        $additionalInfo->id_image = $imageId;
        $additionalInfo->id_address = $address->id;
        $additionalInfo->save();

        return response()->json($additionalInfo->load('image', 'address'), 201);
    }


    public function show($id)
    {
        $info = AdditionalInfo::with('image', 'address.town')->find($id);
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
            'name'               => 'sometimes|string|max:255',
            'text'               => 'sometimes|string|nullable',
            'postal_code'        => 'sometimes|string|max:20',
            'town'               => 'sometimes|string|max:255',
            'street'             => 'sometimes|string|max:255',
            'descriptive_number' => 'sometimes|string|max:20',
            'image_file'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 1) Obrázok (ak prišiel nový)
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('images', 'public');
            $newImage = Image::create(['path' => $path]);

            // zmažeme starý obrázok, ak existuje
            if ($info->id_image) {
                $oldImage = Image::find($info->id_image);
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage->path);
                    $oldImage->delete();
                }
            }
            $info->id_image = $newImage->id;
        }

        // 2) Adresa (ak máme stĺpec id_address, tak môžeme upraviť existujúcu alebo vytvoriť novú)
        if (!$info->id_address) {
            // Ešte nemáme adresu? Tak vytvoríme
            if (isset($validated['postal_code'], $validated['town'], $validated['street'], $validated['descriptive_number'])) {
                $town = Town::firstOrCreate(
                    ['postal_code' => $validated['postal_code']],
                    ['name' => $validated['town']]
                );
                $address = Address::create([
                    'postal_code'        => $town->postal_code,
                    'street'             => $validated['street'],
                    'descriptive_number' => $validated['descriptive_number'],
                ]);
                $info->id_address = $address->id;
            }
        } else {
            // Adresa existuje, tak ju update-neme (ak prišli nové dáta)
            $address = Address::find($info->id_address);
            if ($address) {
                // ak prišli postal_code + town, tak updatujeme aj tie
                if (isset($validated['postal_code']) && isset($validated['town'])) {
                    $town = Town::firstOrCreate(
                        ['postal_code' => $validated['postal_code']],
                        ['name' => $validated['town']]
                    );
                    $address->postal_code = $town->postal_code;
                }
                if (isset($validated['street'])) {
                    $address->street = $validated['street'];
                }
                if (isset($validated['descriptive_number'])) {
                    $address->descriptive_number = $validated['descriptive_number'];
                }
                $address->save();
            }
        }

        // 3) AdditionalInfo polia
        if (isset($validated['name'])) {
            $info->name = $validated['name'];
        }
        if (array_key_exists('text', $validated)) {
            $info->text = $validated['text'];
        }
        $info->save();

        return response()->json($info->load('image', 'address.town'), 200);
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
