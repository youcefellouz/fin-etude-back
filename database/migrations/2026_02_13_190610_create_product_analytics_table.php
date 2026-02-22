<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            
            $table->integer('total_sold')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->integer('times_viewed')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            
            $table->integer('sales_last_7_days')->default(0);
            $table->integer('sales_last_30_days')->default(0);
            $table->decimal('sales_trend', 8, 2)->default(0);
            
            $table->string('performance_category')->nullable();
            
            $table->integer('predicted_sales_next_week')->default(0);
            $table->integer('predicted_sales_next_month')->default(0);
            $table->decimal('restock_recommendation', 10, 2)->default(0);
            $table->boolean('needs_promotion')->default(false);
            
            $table->json('frequently_bought_with')->nullable();
            
            $table->decimal('optimal_price', 10, 2)->nullable();
            $table->decimal('price_elasticity', 5, 2)->default(1);
            
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('performance_category');
            $table->index('sales_trend');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_analytics');
    }
};