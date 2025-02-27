<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Town;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Zobrazí formulár a zoznam adries
    public function create()
    {
        $addresses = Address::with('town')->get();
        $towns = Town::all(); // Potrebné pre výber mesta v dropdown menu

        return view('addresses.create', compact('addresses', 'towns'));
    }

    // Spracuje formulár a uloží novú adresu
    public function store(Request $request)
{
    $validated = $request->validate([
        'street' => 'required|string|max:255',
        'descriptive_number' => 'required|integer',
        'postal_code' => 'required|string|regex:/^\d{3}\s?\d{2}$/', // PSČ formát 000 00 alebo 00000
        'town_name' => 'required|string|max:255',
    ]);
    

    // Skontroluj, či už mesto existuje
    $town = Town::firstOrCreate(
        ['postal_code' => $validated['postal_code']],
        ['name' => $validated['town_name']]
    );

    // Vytvor novú adresu
    $address = Address::create([
        'street' => $validated['street'],
        'descriptive_number' => $validated['descriptive_number'],
        'postal_code' => $town->postal_code,
    ]);

    return redirect()->route('addresses.create')->with('success', 'Adresa bola úspešne pridaná!');
}

}
