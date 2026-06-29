<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EncoursState;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $statusClass = $this->faker->randomElement([
            PropositionState::class,
            EvaluationState::class,
            RevisionState::class,
            RecolteState::class,
            EncoursState::class,
            CompleteState::class,
            ArchiveState::class,
        ]);

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'budget_global' => $this->faker->randomFloat(2, 1000, 100000),
            'but' => json_encode(['goal' => $this->faker->sentence()]),
            'perimetre' => $this->faker->paragraph(),
            'status' => $statusClass,
            'current_stage' => $statusClass::getMorphClass(),
            'proposer_id' => User::factory(),
            'leader_id' => User::factory(),
            'recolte_manager_id' => User::factory(),
        ];
    }

    public function proposition(): static
    {
        return $this->state(['status' => PropositionState::class, 'current_stage' => PropositionState::getMorphClass()]);
    }

    public function evaluation(): static
    {
        return $this->state(['status' => EvaluationState::class, 'current_stage' => EvaluationState::getMorphClass()]);
    }

    public function revision(): static
    {
        return $this->state(['status' => RevisionState::class, 'current_stage' => RevisionState::getMorphClass()]);
    }

    public function recolte(): static
    {
        return $this->state(['status' => RecolteState::class, 'current_stage' => RecolteState::getMorphClass()]);
    }

    public function enCours(): static
    {
        return $this->state(['status' => EncoursState::class, 'current_stage' => EncoursState::getMorphClass()]);
    }

    public function complete(): static
    {
        return $this->state(['status' => CompleteState::class, 'current_stage' => CompleteState::getMorphClass()]);
    }

    public function archive(): static
    {
        return $this->state(['status' => ArchiveState::class, 'current_stage' => ArchiveState::getMorphClass()]);
    }
}
