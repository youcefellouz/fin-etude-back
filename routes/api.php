<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

// ============================================
// 🌍 PUBLIC ROUTES (للجميع - زوار + عملاء)
// ============================================

// Auth Routes
Route::post('register', [UserController::class, 'register']);
Route::post('verify-code', [UserController::class, 'verifyCode']);
Route::post('resend-code', [UserController::class, 'resendCode']);
Route::post('login', [UserController::class, 'login']);

// Articles - Read Only (للعرض فقط)
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::get('articles/{article_id}/stations', [ArticleController::class, 'get_article_stations']);

// Categories - Read Only
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);

// Brands - Read Only
Route::get('brands', [BrandController::class, 'index']);
Route::get('brands/{brand}', [BrandController::class, 'show']);

// Stations - Read Only
Route::get('stations', [StationController::class, 'index']);
Route::get('stations/{station}', [StationController::class, 'show']);
Route::get('stations/{station_id}/articles', [StationController::class, 'get_station_articles']);

// Discounts - Read Only
Route::get('discounts', [DiscountController::class, 'index']);
Route::get('discounts/{discount}', [DiscountController::class, 'show']);
Route::get('discounts/{discount_id}/articles', [DiscountController::class, 'get_discount_articles']);

// Public Analytics (للتوصيات العامة)
Route::prefix('analytics')->group(function () {
    Route::get('/recommendations/trending', [AnalyticsController::class, 'getTrendingProducts']);
    Route::get('/recommendations/products/{articleId}', [AnalyticsController::class, 'getProductRecommendations']);
});

// 👤 CUSTOMER ROUTES (للعملاء المسجلين)

Route::post('orders', [OrderController::class, 'store']);// exeption for guest users
Route::middleware('auth:sanctum')->group(function () {
    
    // Logout
    Route::post('logout', [UserController::class, 'logout']);
    
    // User Info
    Route::get('user', [UserController::class, 'GetUser']);
    
    // Profile Management
    Route::apiResource('profiles', ProfileController::class);
    
    // Orders Management (العميل يشوف طلباته فقط)
    
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}', [OrderController::class, 'update']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);
    Route::get('orders/{order_id}/articles', [OrderController::class, 'get_order_articles']);
    
    // Personalized Recommendations (توصيات شخصية للعميل)
    Route::get('analytics/recommendations/me', [AnalyticsController::class, 'getPersonalizedRecommendations']);
    Route::get('analytics/customers/me', [AnalyticsController::class, 'customerAnalytics']);
});

// ============================================
// 👨‍💼 ADMIN ROUTES (للـ Admin فقط)
// ============================================

Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function () {
    
    // Articles Management (CRUD كامل)
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    Route::post('articles/{article_id}/discounts', [ArticleController::class, 'add_discount_to_article']);
    Route::post('articles/{article_id}/orders', [ArticleController::class, 'add_order_to_article']);
    Route::post('articles/{article_id}/stations', [ArticleController::class, 'add_station_to_article']);
    
    // Categories Management
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
    
    // Brands Management
    Route::post('brands', [BrandController::class, 'store']);
    Route::put('brands/{brand}', [BrandController::class, 'update']);
    Route::delete('brands/{brand}', [BrandController::class, 'destroy']);
    
    // Stocks Management
    Route::apiResource('stocks', StockController::class);
    
    // Stations Management
    Route::post('stations', [StationController::class, 'store']);
    Route::put('stations/{station}', [StationController::class, 'update']);
    Route::delete('stations/{station}', [StationController::class, 'destroy']);
    
    // Discounts Management
    Route::post('discounts', [DiscountController::class, 'store']);
    Route::put('discounts/{discount}', [DiscountController::class, 'update']);
    Route::delete('discounts/{discount}', [DiscountController::class, 'destroy']);
    
    // All Orders (Admin يشوف كل الطلبات)
    Route::get('order/admin', [OrderController::class, 'getallorders']);
    
  
    // 📊 AI ANALYTICS DASHBOARD (Admin Only)
    
    
    Route::prefix('analytics')->group(function () {
        
        // Dashboard الرئيسي
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
        
        // Customer Analytics (تحليلات العملاء)
        Route::get('/customers/top', [AnalyticsController::class, 'topCustomers']);
        
        // Product Analytics (تحليلات المنتجات)
        Route::get('/products/top', [AnalyticsController::class, 'topProducts']);
        Route::get('/products/{articleId}', [AnalyticsController::class, 'productAnalytics']);
        
        // Sales Predictions (التوقعات)
        Route::get('/predictions', [AnalyticsController::class, 'getPredictions']);
        Route::post('/predictions', [AnalyticsController::class, 'generatePredictions']);
        
        // Smart Alerts (التنبيهات الذكية)
        Route::get('/alerts', [AnalyticsController::class, 'getAlerts']);
        Route::patch('/alerts/{alertId}/read', [AnalyticsController::class, 'markAlertAsRead']);
        Route::patch('/alerts/{alertId}/resolve', [AnalyticsController::class, 'resolveAlert']);
        
        // Reports (التقارير)
        Route::get('/reports/sales', [AnalyticsController::class, 'salesReport']);
        
        // Recommendations Performance (أداء التوصيات)
        Route::get('/recommendations/performance', [AnalyticsController::class, 'recommendationPerformance']);
        
        // Operations (عمليات التحديث)
        Route::post('/update-all', [AnalyticsController::class, 'updateAllAnalytics']);
        Route::post('/detect-patterns', [AnalyticsController::class, 'detectPatterns']);

        Route::get('customers/{userId}', [AnalyticsController::class, 'adminCustomerAnalytics']);
    });
});