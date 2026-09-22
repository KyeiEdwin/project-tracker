<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('allocated', 14, 2)->default(0);
            $table->decimal('spent', 14, 2)->default(0);
            $table->string('status', 32)->default('on-track');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'category']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
