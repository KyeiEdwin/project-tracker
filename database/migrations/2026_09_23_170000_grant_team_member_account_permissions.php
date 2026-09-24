<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const PERMISSIONS = [
        'profile.view',
        'profile.edit',
        'setting.view',
    ];

    public function up(): void
    {
        $now = now();

        foreach (self::PERMISSIONS as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'label' => Str::of($name)->replace('.', ' ')->title()->toString(),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $roleId = DB::table('roles')->where('name', 'team_member')->value('id');

        if (! $roleId) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', self::PERMISSIONS)
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
        $roleId = DB::table('roles')->where('name', 'team_member')->value('id');
        $permissionIds = DB::table('permissions')
            ->whereIn('name', self::PERMISSIONS)
            ->pluck('id');

        if ($roleId && $permissionIds->isNotEmpty()) {
            DB::table('permission_role')
                ->where('role_id', $roleId)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')
            ->whereIn('id', $permissionIds)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('permission_role')
                    ->whereColumn('permission_role.permission_id', 'permissions.id');
            })
            ->delete();
    }
};
