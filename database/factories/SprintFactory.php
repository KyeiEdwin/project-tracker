<?php

namespace Database\Factories;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sprint>
 */
class SprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +21 days');

        return [
            'name' => 'Sprint ' . $this->faker->numberBetween(1, 50),
            'goal' => $this->faker->sentence(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['planned', 'active', 'completed']),
        ];
    }

    /**
     * Indicate that the sprint is planned.
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
        ]);
    }

    /**
     * Indicate that the sprint is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDays(7),
            'end_date' => now()->addDays(7),
        ]);
    }

    /**
     * Indicate that the sprint is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => now()->subDays(21),
            'end_date' => now()->subDays(7),
        ]);
    }
}
