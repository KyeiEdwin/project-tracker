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
        Schema::create('dashboard_metrics_cache', function (Blueprint $table) {
            $table->id();
            $table->string('metric_key', 100)->unique();
            $table->string('metric_value', 255);
            $table->string('previous_value', 255)->nullable();
            $table->enum('trend_direction', ['up', 'down', 'stable'])->nullable();
            $table->decimal('trend_percentage', 8, 2)->nullable();
            $table->timestamp('calculated_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['metric_key']);
            $table->index(['calculated_at']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_metrics_cache');
    }
};
