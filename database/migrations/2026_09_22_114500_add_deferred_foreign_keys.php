<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
        });

        Schema::table('backlog_items', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('workflow_id')->references('id')->on('workflows')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('backlog_items', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['workflow_id']);
        });
    }
};
