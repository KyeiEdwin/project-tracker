<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons_learned', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 32);
            $table->text('description')->nullable();
            $table->string('impact', 16)->default('positive');
            $table->date('recorded_on');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'category']);
            $table->index('impact');
            $table->index('recorded_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons_learned');
    }
};
