<?php

namespace App\Services;

use App\Models\CustomerAnalytics;
use App\Models\ProductAnalytics;
use App\Models\SalesPrediction;
use App\Models\SmartAlert;
use App\Models\Order;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function updateAllCustomerAnalytics()
    {
        $users = \App\Models\User::has('orders')->get();
        
        foreach ($users as $user) {
            $analytics = CustomerAnalytics::firstOrCreate(['user_id' => $user->id]);
            $analytics->updateAnalytics();
        }
    }

    public function updateAllProductAnalytics()
    {
        $articles = Article::all();
        
        foreach ($articles as $article) {
            $analytics = ProductAnalytics::firstOrCreate(['article_id' => $article->id]);
            $analytics->updateAnalytics();
            $analytics->predictFutureSales();
        }
    }

    public function generateSalesPredictions($date = null, $periodType = 'day')
    {
        $date = $date ?? now()->addDay();

        $historicalOrders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '<', now())
            ->get();

        if ($historicalOrders->isEmpty()) {
            return null;
        }

        $daysOfData = now()->diffInDays($historicalOrders->min('created_at'));
        $avgDailyOrders = $historicalOrders->count() / max($daysOfData, 1);
        $avgDailyRevenue = $historicalOrders->sum('global_price') / max($daysOfData, 1);
        $avgOrderValue = $historicalOrders->avg('global_price');

        $recentOrders = $historicalOrders->where('created_at', '>=', now()->subDays(30));
        $olderOrders = $historicalOrders->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)]);

        $growthFactor = 1;
        if ($olderOrders->count() > 0) {
            $recentAvg = $recentOrders->count() / 30;
            $olderAvg = $olderOrders->count() / 30;
            $growthFactor = $recentAvg / $olderAvg;
        }

        $multiplier = $this->getPeriodMultiplier($periodType);
        
        $prediction = SalesPrediction::updateOrCreate(
            [
                'prediction_date' => $date,
                'period_type' => $periodType,
            ],
            [
                'predicted_orders' => round($avgDailyOrders * $multiplier * $growthFactor),
                'predicted_revenue' => round($avgDailyRevenue * $multiplier * $growthFactor, 2),
                'predicted_average_order_value' => round($avgOrderValue, 2),
                'confidence_score' => $this->calculateConfidenceScore($daysOfData),
                'model_version' => '1.0',
            ]
        );

        return $prediction;
    }

    private function getPeriodMultiplier($periodType)
    {
        switch ($periodType) {
            case 'day':
                return 1;
            case 'week':
                return 7;
            case 'month':
                return 30;
            default:
                return 1;
        }
    }

    private function calculateConfidenceScore($daysOfData)
    {
        if ($daysOfData >= 90) {
            return 90;
        } elseif ($daysOfData >= 30) {
            return 70;
        } elseif ($daysOfData >= 7) {
            return 50;
        }
        return 30;
    }

    public function detectPatterns()
    {
        $alerts = [];

        $this->detectLowStock($alerts);
        $this->detectSalesDrop($alerts);
        $this->detectAtRiskCustomers($alerts);
        $this->detectPromotionOpportunities($alerts);

        return $alerts;
    }

    private function detectLowStock(&$alerts)
    {
        $lowStockProducts = DB::table('stocks')
            ->select('article_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('article_id')
            ->having('total_quantity', '<', 10)
            ->get();

        foreach ($lowStockProducts as $product) {
            $article = Article::find($product->article_id);
            
            SmartAlert::create([
                'alert_type' => 'low_stock',
                'severity' => $product->total_quantity < 5 ? 'critical' : 'high',
                'title' => 'مخزون منخفض',
                'message' => "المنتج '{$article->name}' لديه مخزون منخفض ({$product->total_quantity} وحدة)",
                'related_entity_type' => 'article',
                'related_entity_id' => $product->article_id,
                'recommended_action' => 'قم بإعادة تموين المخزون في أقرب وقت',
                'importance_score' => 85,
                'metadata' => [
                    'current_quantity' => $product->total_quantity,
                ],
            ]);
        }
    }

    private function detectSalesDrop(&$alerts)
    {
        $productsWithDrop = ProductAnalytics::where('sales_trend', '<', -30)
            ->where('performance_category', '!=', 'dead_stock')
            ->get();

        foreach ($productsWithDrop as $analytics) {
            $article = $analytics->article;
            
            SmartAlert::create([
                'alert_type' => 'sales_drop',
                'severity' => 'medium',
                'title' => 'انخفاض في المبيعات',
                'message' => "المنتج '{$article->name}' يشهد انخفاضاً في المبيعات بنسبة " . abs(round($analytics->sales_trend)) . "%",
                'related_entity_type' => 'article',
                'related_entity_id' => $article->id,
                'recommended_action' => 'فكر في عمل عرض ترويجي أو خصم لتحفيز المبيعات',
                'importance_score' => 70,
                'metadata' => [
                    'sales_trend' => $analytics->sales_trend,
                    'sales_last_30_days' => $analytics->sales_last_30_days,
                ],
            ]);
        }
    }

    private function detectAtRiskCustomers(&$alerts)
    {
        $atRiskCustomers = CustomerAnalytics::where('customer_segment', 'at_risk')
            ->where('loyalty_score', '>=', 50)
            ->get();

        foreach ($atRiskCustomers as $analytics) {
            $user = $analytics->user;
            
            SmartAlert::create([
                'alert_type' => 'customer_churn',
                'severity' => 'medium',
                'title' => 'عميل معرض للخطر',
                'message' => "العميل '{$user->name}' لم يشترِ منذ {$analytics->days_since_last_order} يوم",
                'related_entity_type' => 'user',
                'related_entity_id' => $user->id,
                'recommended_action' => 'أرسل عرضاً خاصاً أو رسالة تذكير لإعادة تفاعل العميل',
                'importance_score' => 65,
                'metadata' => [
                    'days_since_last_order' => $analytics->days_since_last_order,
                    'total_spent' => $analytics->total_spent,
                    'loyalty_score' => $analytics->loyalty_score,
                ],
            ]);
        }
    }

    private function detectPromotionOpportunities(&$alerts)
    {
        $productsNeedingPromotion = ProductAnalytics::where('needs_promotion', true)
            ->where('total_sold', '>', 0)
            ->get();

        foreach ($productsNeedingPromotion as $analytics) {
            $article = $analytics->article;
            
            SmartAlert::create([
                'alert_type' => 'promotion_opportunity',
                'severity' => 'low',
                'title' => 'فرصة ترويجية',
                'message' => "المنتج '{$article->name}' مرشح للترويج لزيادة المبيعات",
                'related_entity_type' => 'article',
                'related_entity_id' => $article->id,
                'recommended_action' => 'أنشئ خصماً بنسبة 10-20% لمدة أسبوع',
                'importance_score' => 50,
                'metadata' => [
                    'current_price' => $article->price,
                    'sales_last_30_days' => $analytics->sales_last_30_days,
                ],
            ]);
        }
    }

    public function getDashboardData()
    {
        $last30Days = now()->subDays(30);
        
        return [
            'total_revenue_30_days' => Order::where('created_at', '>=', $last30Days)
                ->where('status', '!=', 'cancelled')
                ->sum('global_price'),
            
            'total_orders_30_days' => Order::where('created_at', '>=', $last30Days)
                ->where('status', '!=', 'cancelled')
                ->count(),
            
            'average_order_value' => Order::where('created_at', '>=', $last30Days)
                ->where('status', '!=', 'cancelled')
                ->avg('global_price'),
            
            'bestseller_products' => ProductAnalytics::where('performance_category', 'bestseller')
                ->with('article')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get(),
            
            'trending_products' => ProductAnalytics::where('performance_category', 'trending')
                ->with('article')
                ->orderByDesc('sales_trend')
                ->limit(5)
                ->get(),
            
            'vip_customers' => CustomerAnalytics::where('customer_segment', 'vip')
                ->with('user')
                ->orderByDesc('total_spent')
                ->limit(10)
                ->get(),
            
            'at_risk_customers_count' => CustomerAnalytics::where('customer_segment', 'at_risk')->count(),
            
            'active_alerts' => SmartAlert::unresolved()
                ->orderByDesc('importance_score')
                ->limit(10)
                ->get(),
        ];
    }
}