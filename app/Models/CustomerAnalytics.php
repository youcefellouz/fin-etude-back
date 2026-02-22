<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'total_orders',
        'total_spent',
        'average_order_value',
        'days_since_last_order',
        'last_order_date',
        'first_order_date',
        'customer_segment',
        'loyalty_score',
        'favorite_categories',
        'favorite_brands',
        'products_viewed',
        'cart_abandonments',
        'next_purchase_probability',
        'predicted_days_to_next_purchase',
    ];

    protected $casts = [
        'last_order_date' => 'datetime',
        'first_order_date' => 'datetime',
        'favorite_categories' => 'array',
        'favorite_brands' => 'array',
        'total_spent' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'next_purchase_probability' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateAnalytics()
    {
        if (!$this->user_id) {
            return;
        }

        $user = $this->user;
        $orders = $user->orders()->where('status', '!=', 'cancelled')->get();

        if ($orders->isEmpty()) {
            return;
        }

        $this->total_orders = $orders->count();
        $this->total_spent = $orders->sum('global_price');
        $this->average_order_value = $this->total_spent / $this->total_orders;
        
        $lastOrder = $orders->sortByDesc('created_at')->first();
        $firstOrder = $orders->sortBy('created_at')->first();
        
        $this->last_order_date = $lastOrder->created_at;
        $this->first_order_date = $firstOrder->created_at;
        $this->days_since_last_order = now()->diffInDays($this->last_order_date);

        $categories = [];
        foreach ($orders as $order) {
            foreach ($order->articles as $article) {
                $categoryId = $article->category_id;
                if (!isset($categories[$categoryId])) {
                    $categories[$categoryId] = 0;
                }
                $categories[$categoryId]++;
            }
        }
        arsort($categories);
        $this->favorite_categories = array_slice(array_keys($categories), 0, 3, true);

        $this->customer_segment = $this->determineCustomerSegment();
        $this->loyalty_score = $this->calculateLoyaltyScore();

        $this->save();
    }

    private function determineCustomerSegment()
    {
        if ($this->total_orders == 0) {
            return 'new';
        }

        if ($this->days_since_last_order > 90) {
            return 'lost';
        }

        if ($this->days_since_last_order > 30) {
            return 'at_risk';
        }

        if ($this->total_orders >= 10 || $this->total_spent >= 1000) {
            return 'vip';
        }

        if ($this->total_orders >= 3) {
            return 'regular';
        }

        return 'new';
    }

    private function calculateLoyaltyScore()
    {
        $score = 0;

        $score += min($this->total_orders * 3, 30);
        $score += min($this->total_spent / 50, 30);

        if ($this->days_since_last_order <= 7) {
            $score += 20;
        } elseif ($this->days_since_last_order <= 30) {
            $score += 10;
        }

        if ($this->average_order_value >= 100) {
            $score += 20;
        } elseif ($this->average_order_value >= 50) {
            $score += 10;
        }

        return min($score, 100);
    }
}