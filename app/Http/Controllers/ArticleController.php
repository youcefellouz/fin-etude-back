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
    public function index(Request $request)
{
    $query = Article::with(['category', 'brand', 'discounts'])
        ->withSum('stocks', 'quantity');

    if ($request->has('category_id') && $request->category_id) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->has('brand_id') && $request->brand_id) {
        $query->where('brand_id', $request->brand_id);
    }

    if ($request->has('search') && $request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $articles = $query->get()->map(function ($article) {
        $article->total_stock = $article->stocks_sum_quantity ?? 0;
        return $article;
    });

    return response()->json($articles);
}

    public function store(StoreArticleRequest $request)
{
    $data = $request->validated();
    $data['description'] = $data['description'] ?? ''; // ← default empty string

    $article = Article::create($data);

    if ($request->hasFile('image')) {
        $path           = $request->file('image')->store('articles', 'public');
        $article->image = $path;
        $article->save();
    }

    return response()->json($article, 201);
}

   public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validated();
        $data['description'] = $data['description'] ?? '';

        $article->update($data);

        if ($request->hasFile('image')) {
            $path           = $request->file('image')->store('articles', 'public');
            $article->image = $path;
            $article->save();
        }

        return response()->json($article, 202);
    }

    /**
     * Show article and log the view for AI analytics.
     */
    public function show($id)
{
    $article = Article::with(['category', 'brand', 'discounts'])
        ->withSum('stocks', 'quantity')
        ->findOrFail($id);

    UserActivityLog::logActivity('product_view', 'article', $article->id, [
        'article_name' => $article->name,
        'price'        => $article->price,
        'category_id'  => $article->category_id,
    ]);

    ProductAnalytics::where('article_id', $article->id)->increment('times_viewed');

    $article->total_stock = $article->stocks_sum_quantity ?? 0;  

    return response()->json($article, 200);
}

    public function destroy($id)
{
    $article = Article::findOrFail($id);

    // Delete image from storage if exists
    if ($article->image) {
        \Storage::disk('public')->delete($article->image);
    }

    $article->delete();

    return response()->json(null, 204);
}

    public function add_discount_to_article(Request $request, $id)
    {
        $request->validate(['discount_id' => 'required|exists:discounts,id']);
        $article = Article::findOrFail($id);

        if ($article->discounts()->where('discount_id', $request->discount_id)->exists()) {
            return response()->json(['message' => 'Cette remise est déjà appliquée à cet article'], 409);
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