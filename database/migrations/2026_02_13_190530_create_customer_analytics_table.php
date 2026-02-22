<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('session_id')->nullable();
            
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('days_since_last_order')->nullable();
            $table->timestamp('last_order_date')->nullable();
            $table->timestamp('first_order_date')->nullable();
            
            $table->string('customer_segment')->nullable();
            $table->integer('loyalty_score')->default(0);
            
            $table->json('favorite_categories')->nullable();
            $table->json('favorite_brands')->nullable();
            
            $table->integer('products_viewed')->default(0);
            $table->integer('cart_abandonments')->default(0);
            
            $table->decimal('next_purchase_probability', 5, 2)->default(0);
            $table->integer('predicted_days_to_next_purchase')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('customer_segment');
            $table->index('loyalty_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_analytics');
    }
};