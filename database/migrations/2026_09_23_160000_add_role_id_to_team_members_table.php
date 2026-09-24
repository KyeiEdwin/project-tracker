<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('team_id')->constrained('roles')->nullOnDelete();
            $table->index(['role_id', 'status']);
        });

        $roleId = DB::table('roles')->where('name', 'team_member')->value('id');

        if ($roleId !== null) {
            DB::table('team_members')->whereNull('role_id')->update(['role_id' => $roleId]);
        }
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex(['role_id', 'status']);
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};