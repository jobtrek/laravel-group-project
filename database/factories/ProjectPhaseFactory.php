<?php

namespace Database\Factories;

use App\Models\ProjectPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectPhase>
 */
class ProjectPhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'duration' => $this->faker->randomElement(['1 week', '2 weeks', '1 month']),
            'objectifs' => $this->faker->randomElements(['Objective 1', 'Objective 2', 'Objective 3'], 2),
            'livrables' => $this->faker->randomElements(['Deliverable 1', 'Deliverable 2', 'Deliverable 3'], 2),
            'order' => $this->faker->numberBetween(1, 10),
            'project_id' => \App\Models\Project::factory(),
        ];
    }
}
