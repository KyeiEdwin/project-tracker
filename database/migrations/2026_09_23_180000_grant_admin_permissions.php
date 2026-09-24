<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permissionNames = config('authorization.permissions', []);

        foreach ($permissionNames as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'label' => Str::of($name)->replace('.', ' ')->title()->toString(),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $roleId = DB::table('roles')->where('name', 'admin')->value('id');

        if (! $roleId) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        // Permission grants are retained when rolling back application data migrations.
    }
};
