<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
{
    $postId = $request->query('postId');
    $restaurantId = $request->query('restaurantId');

    
    $query = Review::query();
    
    if ($postId) {
        $query->where('id_post', $postId);
    }
    if ($restaurantId) {
        $query->where('id_restaurant', $restaurantId);
    }
    $reviews = $query->get();
    return response()->json($reviews, Response::HTTP_OK);
}


    
    public function show($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($review, Response::HTTP_OK);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail' => 'required|email',
            'id_post'        => 'nullable|integer',
            'id_restaurant'  => 'nullable|integer',
            'text' => 'required|string',
            'evaluation' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $review = Review::create($request->all());

        return response()->json($review, Response::HTTP_CREATED);
    }

    
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->mail !== $request->input('mail') && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemáte právo upraviť tento komentár.'], 403);
        }

        $validated = $request->validate([
            'text'       => 'sometimes|string',
            'evaluation' => 'sometimes|integer|min:1|max:5',
        ]);

        $review->update($validated);

        return response()->json($review, 200);
    }


    public function destroy(Request $request, $id)
    {
        $request->validate([
            'mail' => 'required|email'
        ]);

        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Komentár neexistuje.'], 404);
        }
        $isAdmin = $request->user() && $request->user()->role === 'admin';

        if ($review->mail !== $request->input('mail') && !$isAdmin) {
            return response()->json(['message' => 'Nemáte právo zmazať tento komentár.'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Komentár bol vymazaný.'], 200);
    }

}
