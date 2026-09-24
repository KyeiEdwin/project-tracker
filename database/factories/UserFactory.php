<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $role = Role::query()->firstOrCreate(
            ['name' => 'test_user'],
            ['label' => 'Test User']
        );
        $permissions = collect(config('authorization.permissions', []))->map(fn (string $name) => Permission::query()->firstOrCreate(
            ['name' => $name],
            ['label' => str($name)->replace('.', ' ')->title()]
        ));
        $role->permissions()->syncWithoutDetaching($permissions->pluck('id'));

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'role_id' => $role->id,
            'is_active' => true,
        ];
    }
}