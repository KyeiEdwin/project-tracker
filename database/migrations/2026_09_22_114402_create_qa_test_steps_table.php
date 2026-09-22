<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_test_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qa_test_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('instruction');
            $table->text('expected_result')->nullable();
            $table->text('actual_result')->nullable();
            $table->string('status', 32)->default('pending');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['qa_test_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_test_steps');
    }
};
