<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPrediction extends Model
{
    protected $fillable = [
        'prediction_date',
        'period_type',
        'predicted_revenue',
        'predicted_orders',
        'predicted_average_order_value',
        'category_predictions',
        'brand_predictions',
        'station_predictions',
        'confidence_score',
        'model_version',
        'actual_revenue',
        'actual_orders',
        'accuracy_score',
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'predicted_revenue' => 'decimal:2',
        'predicted_average_order_value' => 'decimal:2',
        'category_predictions' => 'array',
        'brand_predictions' => 'array',
        'station_predictions' => 'array',
        'confidence_score' => 'decimal:2',
        'actual_revenue' => 'decimal:2',
        'accuracy_score' => 'decimal:2',
    ];

    public function updateActuals()
    {
        $startDate = $this->prediction_date;
        $endDate = $this->getEndDate();

        $orders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $this->actual_orders = $orders->count();
        $this->actual_revenue = $orders->sum('global_price');

        if ($this->predicted_revenue > 0) {
            $revenueAccuracy = 100 - (abs($this->actual_revenue - $this->predicted_revenue) / $this->predicted_revenue * 100);
            $ordersAccuracy = 100 - (abs($this->actual_orders - $this->predicted_orders) / $this->predicted_orders * 100);
            
            $this->accuracy_score = ($revenueAccuracy + $ordersAccuracy) / 2;
        }

        $this->save();
    }

    private function getEndDate()
    {
        switch ($this->period_type) {
            case 'day':
                return $this->prediction_date->copy()->endOfDay();
            case 'week':
                return $this->prediction_date->copy()->addWeek()->endOfDay();
            case 'month':
                return $this->prediction_date->copy()->addMonth()->endOfDay();
            default:
                return $this->prediction_date->copy()->endOfDay();
        }
    }

    public function scopeFuture($query)
    {
        return $query->where('prediction_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('prediction_date', '<=', now());
    }
}