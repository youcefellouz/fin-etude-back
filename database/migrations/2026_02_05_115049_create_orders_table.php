<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->decimal('global_price', 10, 2)->default(0);
    $table->string('status')->default('pending');
    $table->enum('city', [
    'tunis','sousse','sfax','bizerte','gabes','monastir',
    'nabeul','tozeur','kairouan','kasserine','guelbes',
    'jendouba','kef','mahdia','medenine','manouba',
    'zaghouan','siliana','ariana','beja','ben arous',
    'sidi bouzid','tataouine','test'
])->default('test');
    $table->string('address');
    $table->enum('payment_method', ['cash', 'card'])->default('cash');
    
    // client with account
    $table->foreignId('user_id')
          ->nullable()
          ->constrained()
          ->nullOnDelete();
    // guest client
    $table->string('guest_name')->nullable();
    $table->string('guest_phone')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
