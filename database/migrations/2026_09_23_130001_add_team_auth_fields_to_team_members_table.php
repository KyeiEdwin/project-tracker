<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('password')->nullable()->after('email');
            $table->rememberToken();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropIndex(['team_id', 'status']);
            $table->dropColumn(['team_id', 'password', 'remember_token']);
        });
    }
};