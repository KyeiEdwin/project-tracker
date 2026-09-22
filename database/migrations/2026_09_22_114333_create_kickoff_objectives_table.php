<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kickoff_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kickoff_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('body');
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['kickoff_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kickoff_objectives');
    }
};
