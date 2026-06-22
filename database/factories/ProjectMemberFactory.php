<?php

namespace Database\Factories;

use App\Models\ProjectMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;

/**
 * @extends Factory<ProjectMember>
 */
class ProjectMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
