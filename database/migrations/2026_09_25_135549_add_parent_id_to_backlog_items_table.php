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
        Schema::table('backlog_items', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('project_id')
                ->constrained('backlog_items')
                ->nullOnDelete();
            
            // Add composite index for efficient hierarchy queries
            $table->index(['project_id', 'parent_id', 'rank'], 'idx_backlog_hierarchy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlog_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex('idx_backlog_hierarchy');
            $table->dropColumn('parent_id');
        });
    }
};
