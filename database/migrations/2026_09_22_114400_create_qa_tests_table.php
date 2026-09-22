<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 32)->default('functional');
            $table->string('status', 32)->default('pending');
            $table->string('priority', 32)->default('medium');
            $table->string('owner')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('type');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_tests');
    }
};
