<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\UserActivityLog;
use App\Models\ProductAnalytics;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    public function store(StoreArticleRequest $request)
    {
        $article = Article::create($request->validated());
        if ($request->hasFile('image')) {
            $path            = $request->file('image')->store('articles', 'public');
            $article->image  = $path;
            $article->save();
        }
        return response()->json($article, 201);
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->validated());
        return response()->json($article, 202);
    }

    public function destroy($id)
    {
        Article::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    /**
     * Show article and log the view for AI analytics.
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);

        // ── Log view activity ──────────────────────────────────────────────
        UserActivityLog::logActivity('product_view', 'article', $article->id, [
            'article_name' => $article->name,
            'price'        => $article->price,
            'category_id'  => $article->category_id,
        ]);

        // Increment times_viewed for conversion rate calculation
        ProductAnalytics::where('article_id', $article->id)
            ->increment('times_viewed');

        return response()->json($article, 200);
    }

    public function add_discount_to_article(Request $request, $id)
    {
        $request->validate(['discount_id' => 'required|exists:discounts,id']);
        $article = Article::findOrFail($id);

        if ($article->discounts()->where('discount_id', $request->discount_id)->exists()) {
            return response()->json(['message' => 'This discount is already applied to this article'], 409);
        }

        $article->discounts()->attach($request->discount_id);
        return response()->json($article->load('discounts'), 200);
    }

    public function get_article_stations($articleId)
    {
        $article  = Article::findOrFail($articleId);
        return response()->json($article->stations, 200);
    }

    public function add_station_to_article(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->stations()->attach($request->station_id, [
            'quantity' => $request->quantity ?? 1,
        ]);
        return response()->json($article->load('stations'), 200);
    }
}