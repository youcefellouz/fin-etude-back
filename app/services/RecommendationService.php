<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ProductAnalytics;
use App\Models\CustomerAnalytics;
use App\Models\RecommendationLog;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Hybrid personalized recommendations for a user.
     * Uses AI collaborative filtering + content-based fallback.
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array
    {
        $allPurchases = $this->getAllPurchasesPayload();
        $articles     = $this->getArticlesPayload();

        $result = $this->ai->personalizedRecommendations($userId, $allPurchases, $articles, $limit);

        if ($result && !empty($result['recommendations'])) {
            $articleIds      = array_column($result['recommendations'], 'article_id');
            $scoresByArticle = array_column($result['recommendations'], 'score', 'article_id');

            $recommended = Article::whereIn('id', $articleIds)->get()
                ->sortBy(fn($a) => -($scoresByArticle[$a->id] ?? 0))
                ->values();

            // Log the recommendation
            $this->logRecommendation(
                $userId, null, $recommended,
                'personalized', $result['algorithm'] ?? 'hybrid'
            );

            return $recommended->toArray();
        }

        // Fallback: trending products
        return $this->getTrendingProducts($limit)->toArray();
    }

    /**
     * Items frequently bought together via Association Rules.
     */
    public function getFrequentlyBoughtTogether(int $articleId, int $limit = 5): array
    {
        // Try AI association rules first
        $orders = $this->getOrdersPayload();

        if (!empty($orders)) {
            $result = $this->ai->associationRules($orders, 0.05, 0.2);

            if ($result && !empty($result['rules'])) {
                $relatedIds = [];
                foreach ($result['rules'] as $rule) {
                    if ($rule['antecedent'] == $articleId) {
                        $relatedIds[] = $rule['consequent'];
                    } elseif ($rule['consequent'] == $articleId) {
                        $relatedIds[] = $rule['antecedent'];
                    }
                    if (count($relatedIds) >= $limit) break;
                }

                if (!empty($relatedIds)) {
                    return Article::whereIn('id', $relatedIds)->get()->toArray();
                }
            }
        }

        // Fallback: product analytics frequently_bought_with
        $analytics = ProductAnalytics::where('article_id', $articleId)->first();
        if ($analytics && !empty($analytics->frequently_bought_with)) {
            $ids = array_slice($analytics->frequently_bought_with, 0, $limit);
            return Article::whereIn('id', $ids)->get()->toArray();
        }

        return $this->getFallbackRecommendations($articleId, $limit)->toArray();
    }

    /**
     * Similar products via item-based collaborative filtering.
     */
    public function getSimilarProducts(int $articleId, int $limit = 5): array
    {
        $allPurchases = $this->getAllPurchasesPayload();
        $articles     = $this->getArticlesPayload();

        $result = $this->ai->similarProducts($articleId, $allPurchases, $articles, $limit);

        if ($result && !empty($result['recommendations'])) {
            $ids = array_column($result['recommendations'], 'article_id');
            return Article::whereIn('id', $ids)->get()->toArray();
        }

        // Fallback: same category
        $article = Article::find($articleId);
        if (!$article) return [];
        return Article::where('category_id', $article->category_id)
            ->where('id', '!=', $articleId)
            ->inRandomOrder()->limit($limit)->get()->toArray();
    }

    /**
     * Trending + bestseller products.
     */
    public function getTrendingProducts(int $limit = 10)
    {
        $analytics = ProductAnalytics::whereIn('performance_category', ['trending', 'bestseller'])
            ->orderByDesc('sales_trend')->limit($limit)->get();

        $ids = $analytics->pluck('article_id');
        return Article::whereIn('id', $ids)->get();
    }

    /**
     * Get recommendation performance metrics.
     */
    public function getRecommendationPerformance(): array
    {
        $total     = RecommendationLog::count();
        $clicked   = RecommendationLog::where('was_clicked',    true)->count();
        $purchased = RecommendationLog::where('was_purchased',  true)->count();

        $ctr = $total > 0 ? round($clicked   / $total * 100, 2) : 0;
        $cvr = $total > 0 ? round($purchased / $total * 100, 2) : 0;

        return [
            'total_recommendations' => $total,
            'clicked'               => $clicked,
            'purchased'             => $purchased,
            'click_through_rate'    => $ctr,
            'conversion_rate'       => $cvr,
            'by_algorithm'          => RecommendationLog::selectRaw('algorithm_used, COUNT(*) as total, SUM(was_clicked) as clicks, SUM(was_purchased) as purchases')
                ->groupBy('algorithm_used')->get(),
        ];
    }

    /**
     * Log a recommendation event.
     */
    public function logRecommendation(?int $userId, ?int $sourceArticleId, $articles, string $type, string $algorithm): ?RecommendationLog
    {
        $data = collect($articles)->map(fn($a) => [
            'id'    => is_array($a) ? $a['id'] : $a->id,
            'name'  => is_array($a) ? $a['name'] : $a->name,
            'price' => is_array($a) ? $a['price'] : $a->price,
        ])->toArray();

        return RecommendationLog::create([
            'user_id'               => $userId,
            'session_id'            => session()->getId(),
            'source_article_id'     => $sourceArticleId,
            'recommended_articles'  => $data,
            'recommendation_type'   => $type,
            'algorithm_used'        => $algorithm,
        ]);
    }

    // ─── Data preparation helpers ─────────────────────────────────────────────

    private function getAllPurchasesPayload(): array
    {
        return DB::table('order_articles')
            ->join('orders', 'orders.id', '=', 'order_articles.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotNull('orders.user_id')
            ->select('orders.user_id', 'order_articles.article_id', 'order_articles.quantity')
            ->get()
            ->map(fn($row) => [
                'user_id'    => $row->user_id,
                'article_id' => $row->article_id,
                'quantity'   => $row->quantity,
            ])->toArray();
    }

    private function getArticlesPayload(): array
    {
        return Article::with('productAnalytics')->get()->map(fn($a) => [
            'article_id'  => $a->id,
            'category_id' => $a->category_id,
            'brand_id'    => $a->brand_id,
            'price'       => (float) $a->price,
            'total_sold'  => $a->productAnalytics?->total_sold ?? 0,
        ])->toArray();
    }

    private function getOrdersPayload(): array
    {
        $orders = DB::table('order_articles')
            ->join('orders', 'orders.id', '=', 'order_articles.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('order_articles.order_id', 'order_articles.article_id')
            ->get()
            ->groupBy('order_id');

        return $orders->map(fn($items, $orderId) => [
            'order_id'    => $orderId,
            'article_ids' => $items->pluck('article_id')->unique()->values()->toArray(),
        ])->values()->toArray();
    }

    private function getFallbackRecommendations(int $articleId, int $limit)
    {
        $article = Article::find($articleId);
        if (!$article) return collect();
        return Article::where('category_id', $article->category_id)
            ->where('id', '!=', $articleId)
            ->inRandomOrder()->limit($limit)->get();
    }
}