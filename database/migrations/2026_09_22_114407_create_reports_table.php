<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('name');
            $table->string('type', 32);
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 32)->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('metrics')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'type']);
            $table->index('generated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
