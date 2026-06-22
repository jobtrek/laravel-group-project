<?php

namespace Database\Factories;

use App\Models\ProjectEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends Factory<ProjectEvaluation>
 */
class ProjectEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portee' => $this->faker->randomFloat(2, 0, 100),
            'impact' => $this->faker->numberBetween(1, 10),
            'confiance' => $this->faker->numberBetween(1, 10),
            'effort' => $this->faker->numberBetween(1, 10),
            'project_id' => Project::factory(),
        ];
    }
}
