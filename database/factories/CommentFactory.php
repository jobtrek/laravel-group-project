<?php

namespace Database\Factories;

use App\Enums\Stage;
use App\Models\Comment;
use App\Models\Project;
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
            'stage' => $this->faker->randomElement(array_column(Stage::cases(), 'value')),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
