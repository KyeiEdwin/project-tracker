<?php

namespace Database\Factories;

use App\Models\TeamMember;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = fake()->randomElement(['manager', 'developer', 'designer', 'tester', 'analyst']);
        $memberRole = Role::query()->firstOrCreate(
            ['name' => 'team_member'],
            ['label' => 'Team Member']
        );
        $permissions = collect([
            'dashboard.team_member.view',
            'member.dashboard.view',
            'member.project.view',
            'member.task.view',
            'member.task.status.update',
            'member.task.complete',
            'profile.view',
            'profile.edit',
            'setting.view',
        ])->map(fn (string $name) => Permission::query()->firstOrCreate(
            ['name' => $name],
            ['label' => str($name)->replace('.', ' ')->title()]
        ));
        $memberRole->permissions()->syncWithoutDetaching($permissions->pluck('id'));

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
            'department' => fake()->randomElement(['Engineering', 'Design', 'Product', 'Quality']),
            'availability' => fake()->numberBetween(50, 100),
            'hourly_rate' => fake()->numberBetween(50, 180),
            'status' => 'active',
            'remember_token' => Str::random(10),
            'role_id' => $memberRole->id,
        ];
    }
}
