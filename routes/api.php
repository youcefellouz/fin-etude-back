<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\ReviewController;


//  PUBLIC ROUTES 


// analytics routes
Route::get('analytics/recommendations/similar/{articleId}', [AnalyticsController::class, 'getSimilarProducts']);
Route::get('analytics/ai-status', [AnalyticsController::class, 'aiStatus']);
Route::get('analytics/recommendations/trending', [AnalyticsController::class, 'getTrendingProducts']);
Route::get('analytics/recommendations/products/{articleId}', [AnalyticsController::class, 'getProductRecommendations']);

// Auth Routes — Rate Limited (5 attempts per minute per IP)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('verify-code', [UserController::class, 'verifyCode']);
    Route::post('resend-code', [UserController::class, 'resendCode']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('verify-reset-code', [UserController::class, 'verifyResetCode']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);
});

// Articles - Read Only
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
/*Route::get('stations', [StationController::class, 'index']);
Route::get('stations/{station}', [StationController::class, 'show']);
Route::get('stations/{station_id}/articles', [StationController::class, 'get_station_articles']);*/

// Discounts - Read Only
Route::get('discounts', [DiscountController::class, 'index']);
Route::get('discounts/{discount}', [DiscountController::class, 'show']);
Route::get('discounts/{discount_id}/articles', [DiscountController::class, 'get_discount_articles']);

// Public Analytics 
/*Route::prefix('analytics')->group(function () {
    Route::get('/recommendations/trending', [AnalyticsController::class, 'getTrendingProducts']);
    Route::get('/recommendations/products/{articleId}', [AnalyticsController::class, 'getProductRecommendations']);
});*/

// technical stations
Route::get('stations/technique', [RepairRequestController::class, 'technicalStations']);

// Reviews
Route::get('articles/{articleId}/reviews', [ReviewController::class, 'index']);

// pay order
    Route::post('orders/{id}/pay', [OrderController::class, 'pay']);
    
// store order for guest and authenticated users
    Route::post('orders', [OrderController::class, 'store']);


//  CUSTOMER ROUTES 


Route::middleware('auth:sanctum')->group(function () {
    
    // Logout
    Route::post('logout', [UserController::class, 'logout']);
    
    // Profile Management
    Route::apiResource('profiles', ProfileController::class);
    
    // Orders Management 
    
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}', [OrderController::class, 'update']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);
    Route::get('orders/{order_id}/articles', [OrderController::class, 'get_order_articles']);
    
    // Personalized Recommendations 
    Route::get('analytics/recommendations/me', [AnalyticsController::class, 'getPersonalizedRecommendations']);
    Route::get('analytics/customers/me', [AnalyticsController::class, 'customerAnalytics']);

    //  Client repair requests: 
    Route::get('repair-requests', [RepairRequestController::class, 'index']);
    Route::post('repair-requests', [RepairRequestController::class, 'store']);
    Route::get('repair-requests/{id}', [RepairRequestController::class, 'show']);
    Route::delete('repair-requests/{id}', [RepairRequestController::class, 'destroy']);

    // Reviews
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
});


//  ADMIN ROUTES 


Route::middleware(['auth:sanctum', 'IsAdmin'])->group(function () {

    Route::get('/forecast/detailed', [AnalyticsController::class, 'detailedForecast']);
    Route::get('/forecast/accuracy',  [AnalyticsController::class, 'forecastAccuracy']);


    Route::get('admin/repair-requests', [RepairRequestController::class, 'adminIndex']);
    Route::put('admin/repair-requests/{id}', [RepairRequestController::class, 'adminUpdate']);


     // User Info
    Route::get('users', [UserController::class, 'index']);
    //Route::get('user', [UserController::class, 'GetUser']);
    
    
    // Articles Management 
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    Route::post('articles/{article_id}/discounts', [ArticleController::class, 'add_discount_to_article']);
    
    // add_order_to_article removed — orders are created via POST /orders directly
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
    Route::get('/stations', [StationController::class, 'index']);    
    // Discounts Management
    Route::post('discounts', [DiscountController::class, 'store']);
    Route::put('discounts/{discount}', [DiscountController::class, 'update']);
    Route::delete('discounts/{discount}', [DiscountController::class, 'destroy']);
    Route::post('discounts/{id}/articles', [DiscountController::class, 'add_article_to_discount']); //
    
    // All Orders 
    Route::get('order/admin', [OrderController::class, 'getallorders']);

    // Reviews
    Route::delete('admin/reviews/{id}', [ReviewController::class, 'adminDestroy']);
    
  
    // AI ANALYTICS DASHBOARD 
    
    
    Route::prefix('analytics')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
        
        // Customer Analytics 
        Route::get('/customers/top', [AnalyticsController::class, 'topCustomers']);
        
        // Product Analytics 
        Route::get('/products/top', [AnalyticsController::class, 'topProducts']);
        Route::get('/products/{articleId}', [AnalyticsController::class, 'productAnalytics']);
        
        // Sales Predictions
        Route::get('/predictions', [AnalyticsController::class, 'getPredictions']);
        Route::post('/predictions', [AnalyticsController::class, 'generatePredictions']);
        Route::get('/forecast/detailed',        [AnalyticsController::class, 'detailedForecast']);    // NEW
        Route::get('/forecast/accuracy',        [AnalyticsController::class, 'forecastAccuracy']);
        
        // Smart Alerts
        Route::get('/alerts', [AnalyticsController::class, 'getAlerts']);
        Route::patch('/alerts/{alertId}/read', [AnalyticsController::class, 'markAlertAsRead']);
        Route::patch('/alerts/{alertId}/resolve', [AnalyticsController::class, 'resolveAlert']);
        
        // Reports 
        Route::get('/reports/sales', [AnalyticsController::class, 'salesReport']);
        
        // Recommendations Performance 
        Route::get('/recommendations/performance', [AnalyticsController::class, 'recommendationPerformance']);
        
        // Operations 
        Route::post('/update-all', [AnalyticsController::class, 'updateAllAnalytics']);
        Route::post('/detect-patterns', [AnalyticsController::class, 'detectPatterns']);

        Route::get('customers/{userId}', [AnalyticsController::class, 'adminCustomerAnalytics']);
    });
});