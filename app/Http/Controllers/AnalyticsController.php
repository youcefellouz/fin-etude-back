<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\RecommendationService;
use App\Models\CustomerAnalytics;
use App\Models\ProductAnalytics;
use App\Models\SalesPrediction;
use App\Models\SmartAlert;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;
    protected $recommendationService;

    public function __construct(AnalyticsService $analyticsService, RecommendationService $recommendationService)
    {
        $this->analyticsService = $analyticsService;
        $this->recommendationService = $recommendationService;
    }

    public function dashboard()
    {
        $data = $this->analyticsService->getDashboardData();
        
        return response()->json($data);
    }

   public function customerAnalytics(Request $request)
{
    // الحصول على user_id من الـ Token
    $userId = $request->user()->id;
    
    $analytics = CustomerAnalytics::where('user_id', $userId)
        ->latest()
        ->first();
    
    if (!$analytics) {
        return response()->json([
            'message' => 'لا توجد تحليلات لك بعد. سيتم تحديثها قريباً!',
            'user' => [
                'id' => $userId,
                'name' => $request->user()->name
            ]
        ], 404);
    }
    
    return response()->json([
        'user' => [
            'id' => $userId,
            'name' => $request->user()->name,
            'email' => $request->user()->email
        ],
        'analytics' => $analytics
    ]);
}

    public function productAnalytics($articleId)
    {
        $analytics = ProductAnalytics::where('article_id', $articleId)
            ->with('article')
            ->first();
        
        if (!$analytics) {
            return response()->json([
                'message' => 'لا توجد تحليلات لهذا المنتج بعد'
            ], 404);
        }

        return response()->json($analytics);
    }

    public function updateAllAnalytics()
    {
        $this->analyticsService->updateAllCustomerAnalytics();
        $this->analyticsService->updateAllProductAnalytics();
        
        return response()->json([
            'message' => 'تم تحديث جميع التحليلات بنجاح'
        ]);
    }

    public function generatePredictions(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'period_type' => 'required|in:day,week,month',
        ]);

        $prediction = $this->analyticsService->generateSalesPredictions(
            $request->date,
            $request->period_type
        );

        if (!$prediction) {
            return response()->json([
                'message' => 'لا توجد بيانات كافية لإنشاء التوقعات'
            ], 400);
        }

        return response()->json($prediction, 201);
    }

    public function getPredictions(Request $request)
    {
        $query = SalesPrediction::query();

        if ($request->has('future_only') && $request->future_only) {
            $query->future();
        }

        if ($request->has('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        $predictions = $query->orderBy('prediction_date', 'desc')->get();

        return response()->json($predictions);
    }

    public function detectPatterns()
    {
        $alerts = $this->analyticsService->detectPatterns();
        
        return response()->json([
            'message' => 'تم كشف الأنماط وإنشاء التنبيهات',
            'alerts_created' => count($alerts)
        ]);
    }

    public function getAlerts(Request $request)
    {
        $query = SmartAlert::query();

        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        if ($request->has('unresolved_only') && $request->unresolved_only) {
            $query->unresolved();
        }

        if ($request->has('severity')) {
            $query->bySeverity($request->severity);
        }

        $alerts = $query->orderByDesc('importance_score')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($alerts);
    }

    public function markAlertAsRead($alertId)
    {
        $alert = SmartAlert::findOrFail($alertId);
        $alert->markAsRead();

        return response()->json([
            'message' => 'تم تعليم التنبيه كمقروء'
        ]);
    }

    public function resolveAlert(Request $request, $alertId)
    {
        $request->validate([
            'resolution_note' => 'nullable|string',
        ]);

        $alert = SmartAlert::findOrFail($alertId);
        $alert->markAsResolved($request->resolution_note);

        return response()->json([
            'message' => 'تم تعليم التنبيه كمحلول'
        ]);
    }

    public function getProductRecommendations($articleId)
    {
        $recommendations = $this->recommendationService->getFrequentlyBoughtTogether($articleId);
        
        return response()->json($recommendations);
    }

    public function getPersonalizedRecommendations(Request $request)
{
    // الحصول على user_id من الـ Token
    $userId = $request->user()->id;
    
    $recommendations = $this->recommendationService
        ->getPersonalizedRecommendations($userId);
    
    return response()->json([
        'user' => [
            'id' => $userId,
            'name' => $request->user()->name,
            'email' => $request->user()->email
        ],
        'recommendations' => $recommendations
    ]);
}

    public function recommendationPerformance()
    {
        $performance = $this->recommendationService->getRecommendationPerformance();
        
        return response()->json($performance);
    }

    public function topCustomers(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $topCustomers = CustomerAnalytics::with('user')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        return response()->json($topCustomers);
    }

    public function topProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $topProducts = ProductAnalytics::with('article')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        return response()->json($topProducts);
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $orders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_revenue' => $orders->sum('global_price'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->avg('global_price'),
            'daily_breakdown' => $this->getDailyBreakdown($orders),
        ];

        return response()->json($report);
    }

    private function getDailyBreakdown($orders)
    {
        return $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'revenue' => $dayOrders->sum('global_price'),
                'orders' => $dayOrders->count(),
            ];
        });
    }
    public function adminCustomerAnalytics($userId)
{
    $analytics = CustomerAnalytics::where('user_id', $userId)
        ->with('user:id,name,email')
        ->latest()
        ->first();
    
    if (!$analytics) {
        return response()->json([
            'message' => 'لا توجد تحليلات لهذا العميل بعد'
        ], 404);
    }
    
    return response()->json($analytics);
}
}