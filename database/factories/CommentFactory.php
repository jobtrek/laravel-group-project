<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RevisionState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph(),
            'stage' => $this->faker->randomElement([RevisionState::getMorphClass(), EncoursState::getMorphClass()]),
            'user_id' => User::inRandomOrder()->first()?->id,
            'project_id' => Project::inRandomOrder()->first()?->id,
        ];
    }
}
