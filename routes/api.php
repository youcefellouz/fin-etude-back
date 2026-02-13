<?php

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
use App\Http\Controllers\SearchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('register', [UserController::class, 'register']);


Route::apiResource('articles', ArticleController::class);
Route::post('articles/{article_id}/discounts', [ArticleController::class, 'add_discount_to_article']);
Route::post('articles/{article_id}/orders', [ArticleController::class, 'add_order_to_article']);
Route::post('articles/{article_id}/stations', [ArticleController::class, 'add_station_to_article']);
Route::get('articles/{article_id}/stations', [ArticleController::class, 'get_article_stations']);



Route::apiResource('categories', CategoryController::class);

Route::apiResource('brands', BrandController::class);


Route::apiResource('profiles', ProfileController::class);

Route::apiResource('stocks', StockController::class);

Route::apiResource('stations', StationController::class);
Route::get('stations/{station_id}/articles', [StationController::class,'get_station_articles']);

Route::apiResource('discounts', DiscountController::class);
Route::get('discounts/{discount_id}/articles', [DiscountController::class,'get_discount_articles']);


Route::apiResource('orders', OrderController::class);
Route::get('orders/{order_id}/articles', [OrderController::class, 'get_order_articles']);

// ============== UNIFIED SEARCH ENDPOINTS ==============
// البحث الموحد يدعم: نص فقط، صورة فقط، أو هجين (نص + صورة)
Route::post('search/unified', [SearchController::class, 'unified']);
Route::get('search/text', [SearchController::class, 'textSearch']);
Route::post('search/image', [SearchController::class, 'imageSearch']);
Route::post('search/hybrid', [SearchController::class, 'hybridSearch']);
Route::post('search/advanced', [SearchController::class, 'advancedSearch']);

