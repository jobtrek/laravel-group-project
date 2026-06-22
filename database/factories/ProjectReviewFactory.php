<?php

namespace Database\Factories;

use App\Models\ProjectReview;
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
            'project_id' => \App\Models\Project::factory(),
            'user_id' => \App\Models\User::factory(),
            'review_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
