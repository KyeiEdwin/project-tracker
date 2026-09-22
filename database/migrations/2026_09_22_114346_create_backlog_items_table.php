<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backlog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sprint_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('task_id')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 32)->default('story');
            $table->unsignedInteger('points')->default(0);
            $table->string('priority', 32)->default('medium');
            $table->string('status', 32)->default('backlog');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['sprint_id', 'rank']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backlog_items');
    }
};
