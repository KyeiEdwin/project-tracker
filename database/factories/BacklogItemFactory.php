<?php

namespace Database\Factories;

use App\Models\BacklogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BacklogItem>
 */
class BacklogItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['epic', 'feature', 'story', 'bug', 'spike', 'task']),
            'points' => $this->faker->randomElement([0, 1, 2, 3, 5, 8, 13, 21]),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['backlog', 'ready', 'in-progress', 'done']),
            'rank' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the backlog item is an epic.
     */
    public function epic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'epic',
            'parent_id' => null,
        ]);
    }

    /**
     * Indicate that the backlog item is a feature.
     */
    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'feature',
        ]);
    }

    /**
     * Indicate that the backlog item is a story.
     */
    public function story(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'story',
        ]);
    }

    /**
     * Indicate that the backlog item is done.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
        ]);
    }
}
