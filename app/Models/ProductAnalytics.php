<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductAnalytics extends Model
{
    protected $fillable = [
        'article_id',
        'total_sold',
        'total_revenue',
        'times_viewed',
        'conversion_rate',
        'sales_last_7_days',
        'sales_last_30_days',
        'sales_trend',
        'performance_category',
        'predicted_sales_next_week',
        'predicted_sales_next_month',
        'restock_recommendation',
        'needs_promotion',
        'frequently_bought_with',
        'optimal_price',
        'price_elasticity',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'sales_trend' => 'decimal:2',
        'restock_recommendation' => 'decimal:2',
        'needs_promotion' => 'boolean',
        'frequently_bought_with' => 'array',
        'optimal_price' => 'decimal:2',
        'price_elasticity' => 'decimal:2',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function updateAnalytics()
    {
        $article = $this->article;

        $orderArticles = DB::table('order_articles')
            ->join('orders', 'orders.id', '=', 'order_articles.order_id')
            ->where('order_articles.article_id', $this->article_id)
            ->where('orders.status', '!=', 'cancelled')
            ->select('order_articles.*', 'orders.created_at')
            ->get();

        $this->total_sold = $orderArticles->sum('quantity');
        $this->total_revenue = $orderArticles->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->sales_last_7_days = $orderArticles->where('created_at', '>=', now()->subDays(7))->sum('quantity');
        $this->sales_last_30_days = $orderArticles->where('created_at', '>=', now()->subDays(30))->sum('quantity');

        if ($this->times_viewed > 0) {
            $this->conversion_rate = ($this->total_sold / $this->times_viewed) * 100;
        }

        $salesLast30to60 = $orderArticles->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->sum('quantity');
        if ($salesLast30to60 > 0) {
            $this->sales_trend = (($this->sales_last_30_days - $salesLast30to60) / $salesLast30to60) * 100;
        }

        $this->performance_category = $this->determinePerformanceCategory();
        $this->needs_promotion = $this->sales_trend < -20 || $this->sales_last_30_days < 5;
        $this->frequently_bought_with = $this->findFrequentlyBoughtWith();

        $this->save();
    }

    private function determinePerformanceCategory()
    {
        if ($this->sales_last_30_days >= 50) {
            return 'bestseller';
        }

        if ($this->sales_trend > 30) {
            return 'trending';
        }

        if ($this->sales_last_30_days < 5 && $this->total_sold < 10) {
            return 'dead_stock';
        }

        if ($this->sales_last_30_days < 15) {
            return 'slow_moving';
        }

        return 'normal';
    }

    private function findFrequentlyBoughtWith()
    {
        $orderIds = DB::table('order_articles')
            ->where('article_id', $this->article_id)
            ->pluck('order_id');

        if ($orderIds->isEmpty()) {
            return [];
        }

        $relatedProducts = DB::table('order_articles')
            ->whereIn('order_id', $orderIds)
            ->where('article_id', '!=', $this->article_id)
            ->select('article_id', DB::raw('COUNT(*) as frequency'))
            ->groupBy('article_id')
            ->orderByDesc('frequency')
            ->limit(5)
            ->get()
            ->pluck('article_id')
            ->toArray();

        return $relatedProducts;
    }

    public function predictFutureSales()
    {
        $avgWeeklySales = $this->sales_last_30_days / 4;
        $trendMultiplier = 1 + ($this->sales_trend / 100);
        
        $this->predicted_sales_next_week = round($avgWeeklySales * $trendMultiplier);
        $this->predicted_sales_next_month = round($avgWeeklySales * 4 * $trendMultiplier);
        
        $this->save();
    }
}