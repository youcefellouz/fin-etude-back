<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'source_article_id',
        'recommended_articles',
        'recommendation_type',
        'algorithm_used',
        'was_clicked',
        'was_purchased',
        'resulting_order_id',
        'click_through_rate',
        'conversion_rate',
    ];

    protected $casts = [
        'recommended_articles' => 'array',
        'was_clicked' => 'boolean',
        'was_purchased' => 'boolean',
        'click_through_rate' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceArticle()
    {
        return $this->belongsTo(Article::class, 'source_article_id');
    }

    public function resultingOrder()
    {
        return $this->belongsTo(Order::class, 'resulting_order_id');
    }

    public function recordClick()
    {
        $this->update(['was_clicked' => true]);
    }

    public function recordPurchase($orderId)
    {
        $this->update([
            'was_purchased' => true,
            'resulting_order_id' => $orderId,
        ]);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('was_purchased', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('recommendation_type', $type);
    }
}