<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Team> */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $name = fake()->company().' Team';

        return [
            'name' => $name,
            'slug' => str($name)->slug().'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->optional()->sentence(),
            'status' => 'active',
        ];
    }
}