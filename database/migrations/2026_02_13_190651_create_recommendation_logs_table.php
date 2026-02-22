<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            
            $table->foreignId('source_article_id')->nullable()->constrained('articles')->onDelete('cascade');
            
            $table->json('recommended_articles');
            
            $table->string('recommendation_type');
            $table->string('algorithm_used')->nullable();
            
            $table->boolean('was_clicked')->default(false);
            $table->boolean('was_purchased')->default(false);
            $table->foreignId('resulting_order_id')->nullable()->constrained('orders')->nullOnDelete();
            
            $table->decimal('click_through_rate', 5, 2)->nullable();
            $table->decimal('conversion_rate', 5, 2)->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('recommendation_type');
            $table->index('was_clicked');
            $table->index('was_purchased');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_logs');
    }
};