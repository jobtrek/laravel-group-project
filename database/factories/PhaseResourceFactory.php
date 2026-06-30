<?php

namespace Database\Factories;

use App\Models\PhaseResource;
use App\Models\ProjectPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhaseResource>
 */
class PhaseResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resource_type' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'work_rate' => $this->faker->numberBetween(10, 100),
            'amount_needed' => $this->faker->randomFloat(2, 1000, 10000),
            'amount_found' => $this->faker->randomFloat(2, 0, 5000),
            'phase_id' => ProjectPhase::inRandomOrder()->first()?->id,
        ];
    }
}
