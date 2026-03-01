<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AIService — Laravel bridge to the Python AI microservice.
 * All communication is via HTTP JSON requests.
 */
class AIService
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ai.url', env('AI_SERVICE_URL', 'http://localhost:8001'));
        $this->timeout = (int) config('services.ai.timeout', 30);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Customer Analytics
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Analyze a single customer with RFM + segmentation.
     */
    public function analyzeCustomer(int $userId, array $orders, array $favoriteCategories = [], array $favoriteBrands = []): ?array
    {
        return $this->post('/api/customers/analyze', [
            'user_id'             => $userId,
            'orders'              => $orders,
            'favorite_categories' => $favoriteCategories,
            'favorite_brands'     => $favoriteBrands,
        ]);
    }

    /**
     * Bulk analyze all customers + K-Means clustering.
     */
    public function bulkAnalyzeCustomers(array $customers): ?array
    {
        return $this->post('/api/customers/bulk-analyze', [
            'customers' => $customers,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sales Forecasting
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Predict future sales (revenue + orders).
     */
    public function forecastSales(array $dailySales, int $forecastDays = 7, string $periodType = 'day'): ?array
    {
        return $this->post('/api/forecasting/predict', [
            'daily_sales'   => $dailySales,
            'forecast_days' => $forecastDays,
            'period_type'   => $periodType,
        ]);
    }

    /**
     * Detailed forecast with category breakdown.
     */
    public function forecastDetailed(array $dailySales, int $forecastDays = 7): ?array
    {
        return $this->post('/api/forecasting/predict-detailed', [
            'daily_sales'   => $dailySales,
            'forecast_days' => $forecastDays,
        ]);
    }

    /**
     * Compute model accuracy via walk-forward validation.
     */
    public function forecastAccuracy(array $dailySales): ?array
    {
        return $this->post('/api/forecasting/accuracy', [
            'daily_sales' => $dailySales,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pattern Detection
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Detect revenue/order anomalies using Z-Score.
     */
    public function detectAnomalies(array $dailyRevenue, array $dailyOrders = []): ?array
    {
        return $this->post('/api/patterns/detect', [
            'daily_revenue' => $dailyRevenue,
            'daily_orders'  => $dailyOrders,
        ]);
    }

    /**
     * Full pattern scan: anomalies + product alerts + customer churn.
     */
    public function fullPatternScan(array $dailyRevenue, array $products = [], array $customers = []): ?array
    {
        return $this->post('/api/patterns/full-scan', [
            'daily_revenue' => $dailyRevenue,
            'products'      => $products,
            'customers'     => $customers,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recommendations
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hybrid personalized recommendations for a user.
     */
    public function personalizedRecommendations(int $userId, array $allPurchases, array $articles, int $limit = 10): ?array
    {
        return $this->post('/api/recommendations/personalized', [
            'target_user_id'    => $userId,
            'all_purchases'     => $allPurchases,
            'articles'          => $articles,
            'limit'             => $limit,
            'exclude_purchased' => true,
        ]);
    }

    /**
     * Similar products via item-based collaborative filtering.
     */
    public function similarProducts(int $articleId, array $allPurchases, array $articles, int $limit = 5): ?array
    {
        return $this->post('/api/recommendations/similar-products', [
            'source_article_id' => $articleId,
            'all_purchases'     => $allPurchases,
            'articles'          => $articles,
            'limit'             => $limit,
        ]);
    }

    /**
     * Mine association rules (frequently bought together).
     */
    public function associationRules(array $orders, float $minSupport = 0.1, float $minConfidence = 0.3): ?array
    {
        return $this->post('/api/recommendations/association-rules', [
            'orders'         => $orders,
            'min_support'    => $minSupport,
            'min_confidence' => $minConfidence,
            'limit'          => 20,
        ]);
    }

    /**
     * Check if the Python AI service is reachable.
     */
    public function isAlive(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal HTTP helper
    // ─────────────────────────────────────────────────────────────────────────

    private function post(string $endpoint, array $payload): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . $endpoint, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("AIService: non-200 response from {$endpoint}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("AIService: Cannot connect to AI service at {$this->baseUrl}{$endpoint}");
            return null;
        } catch (\Exception $e) {
            Log::error("AIService: Unexpected error — {$e->getMessage()}");
            return null;
        }
    }
}