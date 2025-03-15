<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\Restaurant;
use App\Models\Address;
use App\Models\Town;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    
    public function index()
    {
        $restaurants = Restaurant::with('address', 'gallery.images', 'reviews' )->get();
        return response()->json($restaurants);
    }

    public function create()
    {
        return view('restaurants.create');
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'opening_hours' => 'nullable|string',
            'phone_number' => 'nullable|regex:/^(\+?\d{1,3})? ?\d{3} ?\d{3} ?\d{3}$/',
            'postal_code' => 'required|string|max:10',
            'town' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'descriptive_number' => 'required|string|max:10',
            'url_address' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $town = Town::firstOrCreate(['postal_code' => $validated['postal_code']], ['name' => $validated['town']]);

        $address = Address::firstOrCreate([
            'street' => $validated['street'],
            'descriptive_number' => $validated['descriptive_number'],
            'postal_code' => $validated['postal_code'],
        ]);

        $restaurant = Restaurant::create([
            'name' => $validated['name'],
            'opening_hours' => $validated['opening_hours'],
            'phone_number' => $validated['phone_number'],
            'id_address' => $address->id,
            'url_address' => $validated['url_address'],
        ]);

        $gallery = Gallery::firstOrCreate(['id_restaurant' => $restaurant->id]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('images', 'public');
                Image::create([
                    'id_gallery' => $gallery->id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Reštaurácia bola pridaná!',
            'restaurant' => $restaurant]);
    }


    public function show($id)
    {
        $restaurant = Restaurant::with('address.town', 'gallery.images')->findOrFail($id);
        return response()->json($restaurant);
    }

 
    public function update(Request $request, Restaurant $restaurant)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'opening_hours' => 'nullable|string',
        'phone_number' => 'nullable|regex:/^(\+?\d{1,3})? ?\d{3} ?\d{3} ?\d{3}$/',
        'postal_code' => 'required|string|max:10',
        'town' => 'required|string|max:255',
        'street' => 'required|string|max:255',
        'descriptive_number' => 'required|string|max:10',
        'url_address' => 'nullable|string|max:255',
        'images' => 'nullable|array',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    $town = Town::where('postal_code', $validated['postal_code'])->first();
    if ($town) {
        $town->update(['name' => $validated['town']]);
    } else {
        $town = Town::create([
            'postal_code' => $validated['postal_code'],
            'name' => $validated['town']
        ]);
    }

    $address = Address::findOrFail($restaurant->id_address);
    $address->update([
        'street' => $validated['street'],
        'descriptive_number' => $validated['descriptive_number'],
        'postal_code' => $validated['postal_code'],
    ]);

    $restaurant->update($validated);

    if (!$restaurant->gallery) {
        $gallery = Gallery::create(['id_restaurant' => $restaurant->id]);
    } else {
        $gallery = $restaurant->gallery;
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

    return response()->json([
        'message' => 'Reštaurácia bola aktualizovaná!',
        'restaurant' => $restaurant]);
}

  
public function destroy($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            if ($restaurant->gallery) {
                foreach ($restaurant->gallery->images as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
                $restaurant->gallery->delete();
            }

            $restaurant->delete();

            return response()->json([
                'message' => 'Reštaurácia bola vymazaná!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Chyba pri vymazaní reštaurácie.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
