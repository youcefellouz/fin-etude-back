<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_predictions', function (Blueprint $table) {
            $table->id();
            
            $table->date('prediction_date');
            $table->string('period_type');
            
            $table->decimal('predicted_revenue', 12, 2);
            $table->integer('predicted_orders');
            $table->decimal('predicted_average_order_value', 10, 2);
            
            $table->json('category_predictions')->nullable();
            $table->json('brand_predictions')->nullable();
            $table->json('station_predictions')->nullable();
            
            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->string('model_version')->nullable();
            
            $table->decimal('actual_revenue', 12, 2)->nullable();
            $table->integer('actual_orders')->nullable();
            $table->decimal('accuracy_score', 5, 2)->nullable();
            
            $table->timestamps();
            
            $table->index('prediction_date');
            $table->index('period_type');
            $table->unique(['prediction_date', 'period_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_predictions');
    }
};