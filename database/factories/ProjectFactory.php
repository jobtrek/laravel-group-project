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

        $userIds = once(fn () => User::pluck('id')->toArray());

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'but' => ['goal' => $this->faker->sentence()],
            'perimetre' => $this->faker->paragraph(),
            'status' => $statusClass,
            'current_stage' => $statusClass::getMorphClass(),
            'updated_at' => $this->faker->dateTimeBetween('-4 months', '-20 days'),
            'created_at' => $this->faker->dateTimeBetween('-8 months', '-2 days'),
            'proposer_id' => fake()->randomElement($userIds),
            'leader_id' => fake()->randomElement($userIds),
            'recolte_manager_id' => fake()->randomElement($userIds),
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
