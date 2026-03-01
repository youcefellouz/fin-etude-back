<?php

namespace App\Services;

use App\Models\CustomerAnalytics;
use App\Models\ProductAnalytics;
use App\Models\SalesPrediction;
use App\Models\SmartAlert;
use App\Models\Order;
use App\Models\Article;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CUSTOMER ANALYTICS
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Update analytics for a single customer via AI (RFM + K-Means).
     */
    public function updateCustomerAnalytics(int $userId): ?CustomerAnalytics
    {
        $user   = \App\Models\User::with('orders.articles')->find($userId);
        if (!$user) return null;

        $orders = $this->formatUserOrders($user->orders);

        // Compute favorite categories & brands from order history
        [$favCategories, $favBrands] = $this->computeFavorites($user->orders);

        // Call AI microservice
        $result = $this->ai->analyzeCustomer($userId, $orders, $favCategories, $favBrands);

        $analytics = CustomerAnalytics::firstOrCreate(['user_id' => $userId]);

        if ($result && isset($result['data'])) {
            $data = $result['data'];
            $analytics->update([
                'total_orders'                    => $data['total_orders'],
                'total_spent'                     => $data['total_spent'],
                'average_order_value'             => $data['average_order_value'],
                'days_since_last_order'           => $data['days_since_last_order'],
                'customer_segment'                => $data['customer_segment'],
                'loyalty_score'                   => $data['loyalty_score'],
                'favorite_categories'             => $favCategories,
                'favorite_brands'                 => $favBrands,
                'next_purchase_probability'       => $data['next_purchase_probability'],
                'predicted_days_to_next_purchase' => $data['predicted_days_to_next_purchase'],
            ]);
        } else {
            // Fallback: basic calculation without AI
            $analytics->updateAnalytics();
        }

        return $analytics;
    }

    /**
     * Bulk update all customers — uses AI K-Means clustering.
     */
    public function updateAllCustomerAnalytics(): void
    {
        $users = \App\Models\User::has('orders')->with('orders.articles')->get();

        if ($users->isEmpty()) return;

        // Build payload for bulk AI call
        $payload = $users->map(function ($user) {
            [$favCats, $favBrands] = $this->computeFavorites($user->orders);
            return [
                'user_id'             => $user->id,
                'orders'              => $this->formatUserOrders($user->orders),
                'favorite_categories' => $favCats,
                'favorite_brands'     => $favBrands,
            ];
        })->values()->toArray();

        $result = $this->ai->bulkAnalyzeCustomers($payload);

        if ($result && isset($result['data'])) {
            foreach ($result['data'] as $data) {
                $analytics = CustomerAnalytics::firstOrCreate(['user_id' => $data['user_id']]);
                $user      = $users->find($data['user_id']);
                [$favCats, $favBrands] = $user
                    ? $this->computeFavorites($user->orders)
                    : [[], []];

                $analytics->update([
                    'total_orders'                    => $data['total_orders'],
                    'total_spent'                     => $data['total_spent'],
                    'average_order_value'             => $data['average_order_value'],
                    'days_since_last_order'           => $data['days_since_last_order'],
                    'customer_segment'                => $data['customer_segment'],
                    'loyalty_score'                   => $data['loyalty_score'],
                    'favorite_categories'             => $favCats,
                    'favorite_brands'                 => $favBrands,
                    'next_purchase_probability'       => $data['next_purchase_probability'],
                    'predicted_days_to_next_purchase' => $data['predicted_days_to_next_purchase'],
                ]);
            }
        } else {
            // AI unavailable — fallback to local calculation
            Log::warning('AnalyticsService: AI service unavailable, using local fallback');
            foreach ($users as $user) {
                $analytics = CustomerAnalytics::firstOrCreate(['user_id' => $user->id]);
                $analytics->updateAnalytics();
            }
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PRODUCT ANALYTICS
    // ═════════════════════════════════════════════════════════════════════════

    public function updateAllProductAnalytics(): void
    {
        $articles = Article::all();
        foreach ($articles as $article) {
            $analytics = ProductAnalytics::firstOrCreate(['article_id' => $article->id]);
            $analytics->updateAnalytics();
            $analytics->predictFutureSales();
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // SALES FORECASTING
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Generate AI-powered sales predictions.
     */
    public function generateSalesPredictions(?string $date = null, string $periodType = 'day'): ?SalesPrediction
{
    // 🔥 نوحد الفورما ديال التاريخ (DATE فقط)
    $date = $date
        ? \Carbon\Carbon::parse($date)->format('Y-m-d')
        : now()->addDay()->format('Y-m-d');

    $dailySales = $this->getDailySalesHistory(90);

    if (empty($dailySales)) {
        return null;
    }

    // AI Forecast
    $result = $this->ai->forecastSales($dailySales, 1, $periodType);

    if (!$result || !isset($result['revenue_forecast']['predictions'][0])) {
        return $this->fallbackPrediction($date, $periodType, $dailySales);
    }

    $revPred = $result['revenue_forecast']['predictions'][0];
    $ordPred = $result['orders_forecast']['predictions'][0];
    $aovPred = $result['avg_order_value_forecast'][0] ?? null;

    return SalesPrediction::updateOrCreate(
        [
            'prediction_date' => $date,
            'period_type'     => $periodType
        ],
        [
            'predicted_revenue'             => $revPred['value'],
            'predicted_orders'              => (int) $ordPred['value'],
            'predicted_average_order_value' => $aovPred ? $aovPred['value'] : 0,
            'confidence_score'              => $result['revenue_forecast']['confidence_score'] ?? 75,
            'model_version'                 => $result['revenue_forecast']['model'] ?? '2.0',
        ]
    );
}

    /**
     * Generate detailed forecast with category breakdown (30 days).
     */
    public function generateDetailedForecast(int $days = 7): ?array
    {
        $dailySales = $this->getDailySalesHistoryWithCategories(90);
        if (empty($dailySales)) return null;

        return $this->ai->forecastDetailed($dailySales, $days);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PATTERN DETECTION — AI-Powered
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Full AI-powered pattern scan + create SmartAlerts.
     */
    public function detectPatterns(): array
    {
        // Clear old unresolved auto-generated alerts
        SmartAlert::where('is_resolved', false)
            ->whereIn('alert_type', ['low_stock', 'sales_drop', 'customer_churn',
                                     'promotion_opportunity', 'revenue_anomaly'])
            ->delete();

        $dailyRevenue = $this->getDailySalesHistory(60);
        $products     = $this->getProductMetrics();
        $customers    = $this->getCustomerMetrics();

        // Call AI full scan
        $result = $this->ai->fullPatternScan($dailyRevenue, $products, $customers);

        $alerts = [];

        if ($result && isset($result['alerts'])) {
            foreach ($result['alerts'] as $alert) {
                $smartAlert = SmartAlert::create([
                    'alert_type'          => $alert['alert_type'],
                    'severity'            => $alert['severity'],
                    'title'               => $alert['title'],
                    'message'             => $alert['message'],
                    'recommended_action'  => $alert['recommended_action'],
                    'importance_score'    => $alert['importance_score'],
                    'related_entity_type' => $this->resolveEntityType($alert),
                    'related_entity_id'   => $this->resolveEntityId($alert),
                    'metadata'            => $alert['metadata'] ?? null,
                ]);
                $alerts[] = $smartAlert;
            }
        } else {
            // Fallback to local detection
            $this->detectLowStock($alerts);
            $this->detectSalesDrop($alerts);
            $this->detectAtRiskCustomers($alerts);
        }

        return $alerts;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ═════════════════════════════════════════════════════════════════════════

    public function getDashboardData(): array
    {
        $last30Days = now()->subDays(30);

        $totalRevenue = Order::where('created_at', '>=', $last30Days)
            ->where('status', '!=', 'cancelled')->sum('global_price');

        $totalOrders = Order::where('created_at', '>=', $last30Days)
            ->where('status', '!=', 'cancelled')->count();

        $avgOrderValue = Order::where('created_at', '>=', $last30Days)
            ->where('status', '!=', 'cancelled')->avg('global_price');

        // AI-enhanced forecast for next 7 days
        $dailySales = $this->getDailySalesHistory(60);
        $forecast   = null;
        if (!empty($dailySales)) {
            $forecast = $this->ai->forecastSales($dailySales, 7, 'day');
        }

        return [
            'total_revenue_30_days'    => round($totalRevenue, 2),
            'total_orders_30_days'     => $totalOrders,
            'average_order_value'      => round($avgOrderValue ?? 0, 2),
            'bestseller_products'      => ProductAnalytics::where('performance_category', 'bestseller')
                ->with('article')->orderByDesc('total_sold')->limit(5)->get(),
            'trending_products'        => ProductAnalytics::where('performance_category', 'trending')
                ->with('article')->orderByDesc('sales_trend')->limit(5)->get(),
            'vip_customers'            => CustomerAnalytics::where('customer_segment', 'vip')
                ->with('user')->orderByDesc('total_spent')->limit(10)->get(),
            'at_risk_customers_count'  => CustomerAnalytics::where('customer_segment', 'at_risk')->count(),
            'active_alerts'            => SmartAlert::unresolved()
                ->orderByDesc('importance_score')->limit(10)->get(),
            'ai_forecast_next_7_days'  => $forecast ? [
                'revenue_predictions' => $forecast['revenue_forecast']['predictions'] ?? [],
                'confidence_score'    => $forecast['revenue_forecast']['confidence_score'] ?? 0,
                'model'               => $forecast['revenue_forecast']['model'] ?? 'N/A',
            ] : null,
            'ai_service_status'        => $this->ai->isAlive() ? 'online' : 'offline',
        ];
    }

    // ═════════════════════════════════════════════════════════════════════════
    // DATA PREPARATION HELPERS
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Format user orders for AI service payload.
     */
    private function formatUserOrders($orders): array
    {
        return $orders->map(fn($o) => [
            'order_id'    => $o->id,
            'created_at'  => $o->created_at->toISOString(),
            'total_price' => (float) $o->global_price,
            'status'      => $o->status,
        ])->values()->toArray();
    }

    /**
     * Compute favorite categories and brands from order history.
     */
    private function computeFavorites($orders): array
    {
        $categories = [];
        $brands     = [];

        foreach ($orders as $order) {
            if ($order->status === 'cancelled') continue;
            foreach ($order->articles ?? [] as $article) {
                $categories[$article->category_id] = ($categories[$article->category_id] ?? 0) + 1;
                $brands[$article->brand_id]         = ($brands[$article->brand_id] ?? 0) + 1;
            }
        }

        arsort($categories);
        arsort($brands);

        return [
            array_slice(array_keys($categories), 0, 3),
            array_slice(array_keys($brands), 0, 3),
        ];
    }

    /**
     * Get daily sales history formatted for AI service.
     */
    public function getDailySalesHistory(int $days = 90): array
    {
        $sales = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(global_price) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $sales->map(fn($s) => [
            'date'    => $s->date,
            'revenue' => (float) $s->revenue,
            'orders'  => (int) $s->orders,
        ])->values()->toArray();
    }

    /**
     * Get daily sales with category breakdown.
     */
    private function getDailySalesHistoryWithCategories(int $days = 90): array
    {
        $dailySales = $this->getDailySalesHistory($days);

        // Enrich with category breakdown
        foreach ($dailySales as &$day) {
            $cats = DB::table('orders')
                ->join('order_articles', 'orders.id', '=', 'order_articles.order_id')
                ->join('articles', 'articles.id', '=', 'order_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.category_id')
                ->whereDate('orders.created_at', $day['date'])
                ->where('orders.status', '!=', 'cancelled')
                ->selectRaw('articles.category_id, categories.name as category_name, SUM(order_articles.unit_price * order_articles.quantity) as revenue, COUNT(DISTINCT orders.id) as orders')
                ->groupBy('articles.category_id', 'categories.name')
                ->get()
                ->map(fn($c) => [
                    'category_id'   => $c->category_id,
                    'category_name' => $c->category_name,
                    'revenue'       => (float) $c->revenue,
                    'orders'        => (int) $c->orders,
                ])->toArray();

            $day['categories'] = $cats;
        }

        return $dailySales;
    }

    /**
     * Get product metrics for pattern detection.
     */
    private function getProductMetrics(): array
    {
        return Article::with(['productAnalytics'])->get()->map(function ($article) {
            $totalStock  = Stock::where('article_id', $article->id)->sum('quantity');
            $sales60     = $this->getArticleSalesInPeriod($article->id, 60);
            $sales30     = $this->getArticleSalesInPeriod($article->id, 30);
            $sales7      = $this->getArticleSalesInPeriod($article->id, 7);

            return [
                'article_id'         => $article->id,
                'article_name'       => $article->name,
                'total_stock'        => (int) $totalStock,
                'sales_last_7_days'  => $sales7,
                'sales_last_30_days' => $sales30,
                'sales_last_60_days' => $sales60,
                'price'              => (float) $article->price,
            ];
        })->toArray();
    }

    /**
     * Get customer metrics for churn detection.
     */
    private function getCustomerMetrics(): array
    {
        return CustomerAnalytics::whereNotNull('user_id')->get()->map(fn($ca) => [
            'user_id'               => $ca->user_id,
            'days_since_last_order' => (int) ($ca->days_since_last_order ?? 999),
            'total_orders'          => (int) $ca->total_orders,
            'total_spent'           => (float) $ca->total_spent,
            'loyalty_score'         => (int) $ca->loyalty_score,
        ])->toArray();
    }

    private function getArticleSalesInPeriod(int $articleId, int $days): int
    {
        return (int) DB::table('order_articles')
            ->join('orders', 'orders.id', '=', 'order_articles.order_id')
            ->where('order_articles.article_id', $articleId)
            ->where('orders.status', '!=', 'cancelled')
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->sum('order_articles.quantity');
    }

    private function resolveEntityType(array $alert): ?string
    {
        if (isset($alert['article_id'])) return 'article';
        if (isset($alert['user_id']))    return 'user';
        return null;
    }

    private function resolveEntityId(array $alert): ?int
    {
        return $alert['article_id'] ?? $alert['user_id'] ?? null;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // FALLBACK methods (when AI service is offline)
    // ═════════════════════════════════════════════════════════════════════════

    private function fallbackPrediction(string $date, string $periodType, array $dailySales): SalesPrediction
    {
        $revenues = array_column($dailySales, 'revenue');
        $orders   = array_column($dailySales, 'orders');

        $avgRev = count($revenues) ? array_sum($revenues) / count($revenues) : 0;
        $avgOrd = count($orders)   ? array_sum($orders)   / count($orders)   : 0;

        $multiplier = match ($periodType) {
            'week'  => 7,
            'month' => 30,
            default => 1,
        };

        return SalesPrediction::updateOrCreate(
            ['prediction_date' => $date, 'period_type' => $periodType],
            [
                'predicted_revenue'             => round($avgRev * $multiplier, 2),
                'predicted_orders'              => (int) round($avgOrd * $multiplier),
                'predicted_average_order_value' => $avgOrd > 0 ? round($avgRev / $avgOrd, 2) : 0,
                'confidence_score'              => 30,
                'model_version'                 => 'fallback_1.0',
            ]
        );
    }

    private function detectLowStock(array &$alerts): void
    {
        $low = DB::table('stocks')
            ->select('article_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('article_id')
            ->having('total_quantity', '<', 10)
            ->get();

        foreach ($low as $product) {
            $article = Article::find($product->article_id);
            $alerts[] = SmartAlert::create([
                'alert_type'          => 'low_stock',
                'severity'            => $product->total_quantity < 5 ? 'critical' : 'high',
                'title'               => "مخزون منخفض: {$article->name}",
                'message'             => "المخزون {$product->total_quantity} وحدة فقط",
                'recommended_action'  => 'إعادة تموين فوري',
                'importance_score'    => 85,
                'related_entity_type' => 'article',
                'related_entity_id'   => $product->article_id,
                'metadata'            => ['current_quantity' => $product->total_quantity],
            ]);
        }
    }

    private function detectSalesDrop(array &$alerts): void
    {
        $products = ProductAnalytics::where('sales_trend', '<', -30)->get();
        foreach ($products as $analytics) {
            $article = $analytics->article;
            $alerts[] = SmartAlert::create([
                'alert_type'          => 'sales_drop',
                'severity'            => 'medium',
                'title'               => "انخفاض مبيعات: {$article->name}",
                'message'             => "انخفاض " . abs(round($analytics->sales_trend)) . "%",
                'recommended_action'  => 'فكر في عرض ترويجي',
                'importance_score'    => 70,
                'related_entity_type' => 'article',
                'related_entity_id'   => $article->id,
                'metadata'            => ['sales_trend' => $analytics->sales_trend],
            ]);
        }
    }

    private function detectAtRiskCustomers(array &$alerts): void
    {
        $atRisk = CustomerAnalytics::where('customer_segment', 'at_risk')
            ->where('loyalty_score', '>=', 50)->get();
        foreach ($atRisk as $analytics) {
            $user = $analytics->user;
            $alerts[] = SmartAlert::create([
                'alert_type'          => 'customer_churn',
                'severity'            => 'medium',
                'title'               => "عميل معرض للخطر: {$user->name}",
                'message'             => "لم يشترِ منذ {$analytics->days_since_last_order} يوم",
                'recommended_action'  => 'أرسل عرضاً خاصاً',
                'importance_score'    => 65,
                'related_entity_type' => 'user',
                'related_entity_id'   => $user->id,
                'metadata'            => ['days_since_last_order' => $analytics->days_since_last_order],
            ]);
        }
    }
}