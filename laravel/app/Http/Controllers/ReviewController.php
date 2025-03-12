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
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'mail' => 'required|email',
            'id_post' => 'nullable|integer',
            'id_restaurant' => 'nullable|integer',
            'text' => 'required|string',
            'evaluation' => 'required|integer|min:1|max:5',
          ]);
          

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $review->update($request->all());

        return response()->json($review, Response::HTTP_OK);
    }

   
    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_OK);
    }
}
