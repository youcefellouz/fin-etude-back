<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->enum('status', ['en_attente', 'en_cours', 'termine', 'refuse']);
            $table->boolean('warranty')->default(false);
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->dateTime('appointment_date')->nullable();
            $table->text('technician_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};