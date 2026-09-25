<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sprint_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprint_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->unsignedInteger('remaining_points')->default(0);
            $table->unsignedInteger('completed_points')->default(0);
            $table->unsignedInteger('planned_points')->default(0);
            $table->integer('scope_change')->default(0)->comment('Points added/removed on this date');
            $table->timestamps();
            
            // Ensure one snapshot per sprint per day
            $table->unique(['sprint_id', 'snapshot_date'], 'unique_sprint_snapshot');
            $table->index(['sprint_id', 'snapshot_date'], 'idx_sprint_snapshot_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint_snapshots');
    }
};
