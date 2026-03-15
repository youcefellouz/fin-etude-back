<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\RecommendationService;
use App\Services\AIService;
use App\Models\CustomerAnalytics;
use App\Models\ProductAnalytics;
use App\Models\SalesPrediction;
use App\Models\SmartAlert;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService      $analyticsService;
    protected RecommendationService $recommendationService;
    protected AIService             $aiService;

    public function __construct(
        AnalyticsService      $analyticsService,
        RecommendationService $recommendationService,
        AIService             $aiService
    ) {
        $this->analyticsService      = $analyticsService;
        $this->recommendationService = $recommendationService;
        $this->aiService             = $aiService;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ═════════════════════════════════════════════════════════════════════════

    public function dashboard()
    {
        return response()->json($this->analyticsService->getDashboardData());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CUSTOMER ANALYTICS
    // ═════════════════════════════════════════════════════════════════════════

    public function customerAnalytics(Request $request)
    {
        $userId    = $request->user()->id;
        $analytics = CustomerAnalytics::where('user_id', $userId)->latest()->first();

        if (!$analytics) {
            return response()->json([
                'message' => 'Aucune analytique disponible pour le moment. Elles seront générées bientôt.',
                'user'    => ['id' => $userId, 'name' => $request->user()->name],
            ], 404);
        }

        return response()->json([
            'user'      => [
                'id'    => $userId,
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
            ],
            'analytics' => $analytics,
        ]);
    }

    public function adminCustomerAnalytics($userId)
    {
        // Trigger real-time AI analysis for this customer
        $analytics = $this->analyticsService->updateCustomerAnalytics((int) $userId);

        if (!$analytics) {
            return response()->json(['message' => 'Client non trouvé'], 404);
        }

        return response()->json($analytics->load('user:id,name,email'));
    }

    public function topCustomers(Request $request)
    {
        $limit = $request->get('limit', 10);
        return response()->json(
            CustomerAnalytics::with('user')->orderByDesc('total_spent')->limit($limit)->get()
        );
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PRODUCT ANALYTICS
    // ═════════════════════════════════════════════════════════════════════════

    public function productAnalytics($articleId)
    {
        $analytics = ProductAnalytics::where('article_id', $articleId)->with('article')->first();
        if (!$analytics) {
            return response()->json(['message' => 'Aucune analytique trouvée pour ce produit'], 404);
        }
        return response()->json($analytics);
    }

    public function topProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        return response()->json(
            ProductAnalytics::with('article')->orderByDesc('total_sold')->limit($limit)->get()
        );
    }

    // ═════════════════════════════════════════════════════════════════════════
    // SALES FORECASTING (AI-Powered)
    // ═════════════════════════════════════════════════════════════════════════

    public function generatePredictions(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'period_type' => 'required|in:day,week,month',
        ]);

        $prediction = $this->analyticsService->generateSalesPredictions(
            $request->date,
            $request->period_type
        );

        if (!$prediction) {
            return response()->json(['message' => 'Données insuffisantes pour générer des prévisions'], 400);
        }

        return response()->json($prediction, 201);
    }

    /**
     * AI-powered 7-day detailed forecast with category breakdown.
     */
    public function detailedForecast(Request $request)
    {
        $days   = $request->get('days', 7);
        $result = $this->analyticsService->generateDetailedForecast($days);

        if (!$result) {
            return response()->json(['message' => 'Données insuffisantes'], 400);
        }

        return response()->json($result);
    }

    /**
     * Forecast model accuracy metrics (walk-forward validation).
     */
    public function forecastAccuracy()
    {
        $dailySales = $this->analyticsService->getDailySalesHistory(90);

        if (count($dailySales) < 10) {
            return response()->json(['message' => 'Moins de 10 jours de données disponibles'], 400);
        }

        $result = $this->aiService->forecastAccuracy($dailySales);
        return response()->json($result ?? ['message' => 'Service IA indisponible']);
    }

    public function getPredictions(Request $request)
    {
        $query = SalesPrediction::query();
        if ($request->boolean('future_only'))  $query->future();
        if ($request->has('period_type'))      $query->where('period_type', $request->period_type);

        return response()->json($query->orderBy('prediction_date', 'desc')->get());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PATTERN DETECTION + ALERTS
    // ═════════════════════════════════════════════════════════════════════════

    public function detectPatterns()
    {
        $alerts = $this->analyticsService->detectPatterns();
        return response()->json([
            'message'       => 'Détection de modèles terminée',
            'alerts_created' => count($alerts),
        ]);
    }

    public function getAlerts(Request $request)
    {
        $query = SmartAlert::query();
        if ($request->boolean('unread_only'))    $query->unread();
        if ($request->boolean('unresolved_only')) $query->unresolved();
        if ($request->has('severity'))            $query->bySeverity($request->severity);

        return response()->json(
            $query->orderByDesc('importance_score')->orderByDesc('created_at')->get()
        );
    }

    public function markAlertAsRead($alertId)
    {
        SmartAlert::findOrFail($alertId)->markAsRead();
        return response()->json(['message' => 'Alerte marquée comme lue']);
    }

    public function resolveAlert(Request $request, $alertId)
    {
        $request->validate(['resolution_note' => 'nullable|string']);
        SmartAlert::findOrFail($alertId)->markAsResolved($request->resolution_note);
        return response()->json(['message' => 'Alerte résolue']);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // RECOMMENDATIONS
    // ═════════════════════════════════════════════════════════════════════════

    public function getProductRecommendations($articleId)
    {
        $recommendations = $this->recommendationService->getFrequentlyBoughtTogether((int) $articleId);
        return response()->json($recommendations);
    }

    public function getSimilarProducts($articleId)
    {
        $similar = $this->recommendationService->getSimilarProducts((int) $articleId);
        return response()->json($similar);
    }

    public function getPersonalizedRecommendations(Request $request)
    {
        $user            = $request->user();
        $recommendations = $this->recommendationService->getPersonalizedRecommendations($user->id);

        return response()->json([
            'user'            => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'recommendations' => $recommendations,
        ]);
    }

    public function getTrendingProducts()
    {
        return response()->json($this->recommendationService->getTrendingProducts(10));
    }

    public function recommendationPerformance()
    {
        return response()->json($this->recommendationService->getRecommendationPerformance());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // OPERATIONS
    // ═════════════════════════════════════════════════════════════════════════

    public function updateAllAnalytics()
    {
        $this->analyticsService->updateAllCustomerAnalytics();
        $this->analyticsService->updateAllProductAnalytics();
        $this->analyticsService->generateSalesPredictions(now(), 'day');  
        $this->analyticsService->detectPatterns();   
        return response()->json(['message' => 'Toutes les analyses ont été mises à jour']);
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate   = $request->get('end_date', now());

        $orders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')->get();

        return response()->json([
            'period'          => ['start' => $startDate, 'end' => $endDate],
            'total_revenue'   => $orders->sum('global_price'),
            'total_orders'    => $orders->count(),
            'average_order_value' => $orders->avg('global_price'),
            'daily_breakdown' => $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'))
                ->map(fn($d) => ['revenue' => $d->sum('global_price'), 'orders' => $d->count()]),
        ]);
    }

    /**
     * Check AI service health.
     */
    public function aiStatus()
    {
        $alive = $this->aiService->isAlive();
        return response()->json([
            'status'  => $alive ? 'online' : 'offline',
            'url'     => config('services.ai.url', env('AI_SERVICE_URL', 'http://localhost:8001')),
        ]);
    }
}