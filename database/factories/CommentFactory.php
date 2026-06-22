<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;

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
            'stage' => $this->faker->randomElement(['planning', 'development', 'testing', 'deployment']),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
