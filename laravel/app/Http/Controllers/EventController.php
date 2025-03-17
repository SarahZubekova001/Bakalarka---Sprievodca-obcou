<?php


namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Address;
use App\Models\Town;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['address.town', 'image'])->orderBy('date')->orderBy('time')->get();
        return response()->json($events, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'date'    => 'required|date',
            'time'    => 'nullable|date_format:H:i',
            'text'    => 'nullable|string',
            'postal_code'         => 'nullable|string|max:10',
            'town'                => 'nullable|string|max:255',
            'street'              => 'nullable|string|max:255',
            'descriptive_number'  => 'nullable|string|max:10',
            'image_file'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $addressId = null;
        if (!empty($validated['postal_code']) && !empty($validated['town']) && !empty($validated['street'])) {
            $town = Town::firstOrCreate(
                ['postal_code' => $validated['postal_code']],
                ['name' => $validated['town']]
            );

            $address = Address::create([
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'descriptive_number' => $validated['descriptive_number'] ?? 0,
            ]);
            $addressId = $address->id;
        }

        $imageId = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('images','public');
            $img = Image::create(['path' => $path]);
            $imageId = $img->id;
        }

        $event = Event::create([
            'name'       => $validated['name'],
            'date'       => $validated['date'],
            'time'       => $validated['time'] ?? null,
            'text'       => $validated['text'] ?? null,
            'id_address' => $addressId,
            'id_image'   => $imageId,
        ]);

        return response()->json($event->load(['address.town','image']), 201);
    }

    public function show($id)
    {
        $event = Event::with(['address.town','image'])->find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        return response()->json($event, 200);
    }

    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'date' => 'required|date',
        'time' => 'nullable',
        'text' => 'nullable|string',
        'postal_code' => 'nullable|string|max:10',
        'town' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'descriptive_number' => 'nullable|string|max:10',
        'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->input('remove_image') == '1') {
        if ($event->id_image) {
            $oldImage = Image::find($event->id_image);
            $event->id_image = null;
            $event->save();

            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
        }
    }

    if ($request->hasFile('image_file')) {
        if ($event->id_image) {
            $oldImage = Image::find($event->id_image);
            $event->id_image = null;
            $event->save();

            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
        }

        $path = $request->file('image_file')->store('images', 'public');
        $newImage = Image::create(['path' => $path]);
        $event->id_image = $newImage->id;
    }

    $event->name = $validated['name'];
    $event->date = $validated['date'];
    $event->time = $validated['time'] ?? null;
    $event->text = $validated['text'] ?? null;

    $event->save();

    return response()->json($event->load(['address.town','image']), 200);
}


    public function destroy($id)
    {
        $event = Event::findOrFail($id);
            $image = $event->image; 

            $event->delete();

            if ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            return response()->json(['message' => 'Sezóna bola vymazaná'], 200);
    }

    public function destroyImage($id)
    {
        $image = Image::findOrFail($id);

        $event = Event::where('id_image', $image->id)->first();
        if ($event) {
            $event->id_image = null;
            $event->save();
        }

        \Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Obrázok vymazaný'], 200);
    }

}
