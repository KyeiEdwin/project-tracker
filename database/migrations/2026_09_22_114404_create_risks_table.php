<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 32);
            $table->string('probability', 16);
            $table->string('impact', 16);
            $table->string('status', 32)->default('open');
            $table->string('owner')->nullable();
            $table->text('mitigation')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['probability', 'impact']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
