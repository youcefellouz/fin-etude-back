<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ProductAnalytics;
use App\Models\CustomerAnalytics;
use App\Models\Order;
use App\Models\RecommendationLog;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function getFrequentlyBoughtTogether($articleId, $limit = 5)
    {
        $analytics = ProductAnalytics::where('article_id', $articleId)->first();
        
        if (!$analytics || empty($analytics->frequently_bought_with)) {
            return $this->getFallbackRecommendations($articleId, $limit);
        }

        $recommendedIds = array_slice($analytics->frequently_bought_with, 0, $limit);
        
        return Article::whereIn('id', $recommendedIds)->get();
    }

    public function getPersonalizedRecommendations($userId, $limit = 10)
    {
        $customerAnalytics = CustomerAnalytics::where('user_id', $userId)->first();
        
        if (!$customerAnalytics) {
            return $this->getTrendingProducts($limit);
        }

        $favoriteCategories = $customerAnalytics->favorite_categories ?? [];
        $favoriteBrands = $customerAnalytics->favorite_brands ?? [];

        $query = Article::query();

        if (!empty($favoriteCategories)) {
            $query->whereIn('category_id', $favoriteCategories);
        }

        if (!empty($favoriteBrands)) {
            $query->orWhereIn('brand_id', $favoriteBrands);
        }

        $purchasedArticleIds = DB::table('order_articles')
            ->join('orders', 'orders.id', '=', 'order_articles.order_id')
            ->where('orders.user_id', $userId)
            ->pluck('order_articles.article_id')
            ->unique();

        if ($purchasedArticleIds->isNotEmpty()) {
            $query->whereNotIn('id', $purchasedArticleIds);
        }

        return $query->inRandomOrder()->limit($limit)->get();
    }

    public function getTrendingProducts($limit = 10)
    {
        $trendingAnalytics = ProductAnalytics::where('performance_category', 'trending')
            ->orWhere('performance_category', 'bestseller')
            ->orderByDesc('sales_trend')
            ->limit($limit)
            ->get();

        $articleIds = $trendingAnalytics->pluck('article_id');
        
        return Article::whereIn('id', $articleIds)->get();
    }

    public function getSimilarProducts($articleId, $limit = 5)
    {
        $article = Article::findOrFail($articleId);
        
        return Article::where('category_id', $article->category_id)
            ->where('id', '!=', $articleId)
            ->whereHas('productAnalytics', function ($query) {
                $query->where('performance_category', '!=', 'dead_stock');
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    private function getFallbackRecommendations($articleId, $limit)
    {
        $article = Article::findOrFail($articleId);
        
        return Article::where('category_id', $article->category_id)
            ->where('id', '!=', $articleId)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function logRecommendation($userId, $sourceArticleId, $recommendedArticles, $type, $algorithm)
    {
        $recommendedData = $recommendedArticles->map(function ($article) {
            return [
                'id' => $article->id,
                'name' => $article->name,
                'price' => $article->price,
            ];
        })->toArray();

        return RecommendationLog::create([
            'user_id' => $userId,
            'session_id' => session()->getId(),
            'source_article_id' => $sourceArticleId,
            'recommended_articles' => $recommendedData,
            'recommendation_type' => $type,
            'algorithm_used' => $algorithm,
        ]);
    }

    public function getRecommendationPerformance()
    {
        $totalRecommendations = RecommendationLog::count();
        $clickedRecommendations = RecommendationLog::where('was_clicked', true)->count();
        $purchasedRecommendations = RecommendationLog::where('was_purchased', true)->count();

        $clickThroughRate = $totalRecommendations > 0 
            ? ($clickedRecommendations / $totalRecommendations) * 100 
            : 0;

        $conversionRate = $totalRecommendations > 0 
            ? ($purchasedRecommendations / $totalRecommendations) * 100 
            : 0;

        return [
            'total_recommendations' => $totalRecommendations,
            'clicked' => $clickedRecommendations,
            'purchased' => $purchasedRecommendations,
            'click_through_rate' => round($clickThroughRate, 2),
            'conversion_rate' => round($conversionRate, 2),
        ];
    }
}