<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'portee' => $this->faker->randomFloat(2, 1, 50),
            'impact' => $this->faker->numberBetween(1, 5),
            'confiance' => $this->faker->numberBetween(1, 100),
            'effort' => $this->faker->numberBetween(1, 5),
            'project_id' => Project::factory(),
        ];
    }
}
