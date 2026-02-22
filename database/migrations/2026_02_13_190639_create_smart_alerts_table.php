<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_alerts', function (Blueprint $table) {
            $table->id();
            
            $table->string('alert_type');
            $table->string('severity');
            
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable();
            
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            
            $table->text('recommended_action')->nullable();
            
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();
            
            $table->decimal('importance_score', 5, 2)->default(0);
            
            $table->timestamps();
            
            $table->index('alert_type');
            $table->index('severity');
            $table->index('is_read');
            $table->index('is_resolved');
            $table->index(['related_entity_type', 'related_entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_alerts');
    }
};