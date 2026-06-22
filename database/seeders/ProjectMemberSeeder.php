<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectMemberSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectsSeeder first.');

            return;
        }

        foreach ($projects as $project) {
            $memberIds = [$project->proposer_id];

            if ($project->leader_id && $project->leader_id !== $project->proposer_id) {
                $memberIds[] = $project->leader_id;
            }

            $project->members()->syncWithoutDetaching($memberIds);
        }
    }
}
