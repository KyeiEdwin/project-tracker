<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('task_id')->nullable()->index();
            $table->date('work_date');
            $table->decimal('hours', 8, 2);
            $table->string('description')->nullable();
            $table->boolean('is_billable')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('work_date');
            $table->index(['project_id', 'work_date']);
            $table->index(['team_member_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
