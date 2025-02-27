<?php

namespace App\Http\Controllers;

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
        $restaurants = Restaurant::with('address', 'gallery.images')->get();
        return view('restaurants.index', compact('restaurants'));
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

    return redirect()->route('restaurants.index')->with('success', 'Reštaurácia bola pridaná!');
}


    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }


    public function edit(Restaurant $restaurant)
    {
        return view('restaurants.edit', [
            'restaurant' => $restaurant,
            'images' => $restaurant->gallery ? $restaurant->gallery->images : [], // ✅ Pridali sme obrázky
        ]);
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

    $address = Address::findOrFail($restaurant->id_address);
    $address->update([
        'street' => $validated['street'],
        'descriptive_number' => $validated['descriptive_number'],
        'postal_code' => $validated['postal_code'],
    ]);

    $restaurant->update($validated);

    // Získanie galérie reštaurácie
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

    return redirect()->route('restaurants.index')->with('success', 'Reštaurácia bola aktualizovaná!');
}

  
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return redirect()->route('restaurants.index')->with('success', 'Reštaurácia bola vymazaná!');
    }
}
