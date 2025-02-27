<?php

namespace App\Http\Controllers;

use App\Models\Town;
use Illuminate\Http\Request;

class TownController extends Controller
{
    public function index()
    {
        return response()->json(Town::with('addresses')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'postal_code' => 'required|integer|unique:towns,postal_code',
            'name' => 'required|string',
        ]);

        $town = Town::create($validated);
        return response()->json($town, 201);
    }

    public function show(Town $town)
    {
        return response()->json($town->load('addresses'));
    }

    public function update(Request $request, Town $town)
    {
        $validated = $request->validate([
            'name' => 'string',
        ]);

        $town->update($validated);
        return response()->json($town);
    }

    public function destroy(Town $town)
    {
        $town->delete();
        return response()->json(null, 204);
    }
}
