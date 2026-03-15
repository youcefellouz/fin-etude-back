<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index($articleId)
    {
        $reviews = Review::where('article_id', $articleId)
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function store(StoreReviewRequest $request)
{
    $user = Auth::user();

    $alreadyReviewed = Review::where('user_id', $user->id)
        ->where('article_id', $request->article_id)
        ->exists();

    if ($alreadyReviewed) {
        return response()->json([
            'message' => 'you already gave a review to this product'
        ], 422);
    }

    $review = Review::create([
        'user_id'    => $user->id,
        'article_id' => $request->article_id,
        'rating'     => $request->rating,
        'comment'    => $request->comment,
    ]);

    return response()->json($review->load('user:id,name'), 201);
}

    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())
            ->findOrFail($id);

        $review->delete();
        return response()->json(null, 204);
    }

    public function adminDestroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }
}