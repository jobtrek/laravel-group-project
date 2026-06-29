<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\States\ApprovedState;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EncoursState;
use App\Models\States\EvaluationState;
use App\Models\States\ReadyState;
use App\Models\States\RecolteState;
use App\Models\States\RefusedState;
use App\Models\States\SubmittedState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusStates = [
            SubmittedState::class,
            ApprovedState::class,
            RefusedState::class,
            EvaluationState::class,
            ArchiveState::class,
            RecolteState::class,
            ReadyState::class,
            EncoursState::class,
            CompleteState::class,
        ];

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'budget_global' => $this->faker->randomFloat(2, 1000, 100000),
            'but' => json_encode(['goal' => $this->faker->sentence()]),
            'perimetre' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statusStates),
            'current_stage' => $this->faker->randomElement($statusStates),
            'proposer_id' => User::factory(),
            'leader_id' => User::factory(),
            'recolte_manager_id' => User::factory(),
        ];
    }
}
