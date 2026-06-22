<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectReviewSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::whereIn('status', ['submitted', 'modification', 'approved', 'refused'])->get();
        $directionUsers = User::where('role', 'direction')->get();

        if ($projects->isEmpty() || $directionUsers->isEmpty()) {
            return;
        }

        $statuses = ['approved', 'refused', 'needs_more_info'];

        foreach ($projects as $project) {
            foreach ($directionUsers as $user) {
                $project->reviews()->create([
                    'user_id' => $user->id,
                    'review_status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $activeProjects = Project::whereIn('status', ['active', 'completed'])->get();
        $leaders = User::where('role', 'project_lead')->get()->merge($directionUsers);

        foreach ($activeProjects as $project) {
            foreach ($leaders as $user) {
                $project->reviews()->create([
                    'user_id' => $user->id,
                    'review_status' => 'approved',
                ]);
            }
        }
    }
}
