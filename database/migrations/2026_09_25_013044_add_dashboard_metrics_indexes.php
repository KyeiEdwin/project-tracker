<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes to optimize dashboard metrics queries
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Optimize overdue tasks query
            $table->index(['due_date', 'status'], 'idx_tasks_due_status');
            
            // Optimize tasks by created_at for trend calculation
            $table->index(['status', 'created_at'], 'idx_tasks_status_created');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Optimize project status queries
            $table->index(['status', 'created_at'], 'idx_projects_status_created');
            $table->index(['status', 'updated_at'], 'idx_projects_status_updated');
        });

        Schema::table('milestones', function (Blueprint $table) {
            // Optimize milestone queries
            $table->index(['status', 'due_date'], 'idx_milestones_status_due');
        });

        Schema::table('team_members', function (Blueprint $table) {
            // Optimize team member counting
            $table->index('created_at', 'idx_team_members_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_due_status');
            $table->dropIndex('idx_tasks_status_created');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_status_created');
            $table->dropIndex('idx_projects_status_updated');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropIndex('idx_milestones_status_due');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex('idx_team_members_created');
        });
    }
};
