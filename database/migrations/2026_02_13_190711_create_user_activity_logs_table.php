<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            
            $table->string('activity_type');
            
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            
            $table->json('metadata')->nullable();
            
            $table->string('referrer')->nullable();
            $table->string('device_type')->nullable();
            
            $table->timestamp('created_at');
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('activity_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};