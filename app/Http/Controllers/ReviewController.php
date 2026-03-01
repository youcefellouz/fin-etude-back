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

        $purchased = Order::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('articles', function ($q) use ($request) {
                $q->where('article_id', $request->article_id);
            })
            ->exists();

        if (!$purchased) {
            return response()->json([
                'message' => 'Vous devez acheter ce produit avant de le noter.'
            ], 422);
        }

        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('article_id', $request->article_id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'message' => 'Vous avez déjà donné un avis sur ce produit.'
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