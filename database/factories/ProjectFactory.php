<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);

        return [
            'name' => rtrim($name, '.'),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('######'),
            'description' => fake()->optional()->paragraph(),
            'project_type' => fake()->randomElement(['agile', 'predictive', 'hybrid']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => fake()->randomElement(['planning', 'in-progress', 'on-hold', 'completed']),
            'start_date' => fake()->optional()->date(),
            'due_date' => fake()->optional()->date(),
            'budget' => fake()->optional()->randomFloat(2, 1000, 250000),
            'spent' => 0,
            'progress' => fake()->numberBetween(0, 100),
            'team' => fake()->optional()->randomElement([
                'Development Team',
                'Marketing Team',
                'Design Team',
                'QA Team',
            ]),
            'client' => fake()->optional()->company(),
            'settings' => null,
            'owner_id' => null,
        ];
    }
}
