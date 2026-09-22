<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sprint_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('milestone_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('workflow_id')->nullable()->index();
            $table->foreignId('team_member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 32)->default('pending');
            $table->string('priority', 32)->default('medium');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->decimal('estimate_hours', 8, 2)->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->unsignedInteger('kanban_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('due_date');
            $table->index('priority');
            $table->index(['status', 'kanban_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
