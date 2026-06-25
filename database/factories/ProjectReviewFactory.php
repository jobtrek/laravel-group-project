<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectReview>
 */
class ProjectReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'review_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
