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
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
            'but' => ['goal' => $this->faker->sentence()],
            'perimetre' => $this->faker->paragraph(),
            'status' => $statusClass,
            'current_stage' => $statusClass::getMorphClass(),
            'updated_at' => $this->faker->dateTimeBetween('-4 months', '-20 days'),
            'proposer_id' => fn () => User::inRandomOrder()->first()?->id ?? User::factory(),
            'leader_id' => User::inRandomOrder()->first()?->id,
            'recolte_manager_id' => User::inRandomOrder()->first()?->id,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Project $project) {
            $project->evaluation()->create([
                'portee' => fake()->numberBetween(0, 50),
                'impact' => fake()->numberBetween(1, 5),
                'confiance' => fake()->numberBetween(0, 100),
                'effort' => fake()->numberBetween(1, 5),
            ]);
        });
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
