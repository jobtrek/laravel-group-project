<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectsSeeder first.');

            return;
        }

        $evaluations = [
            ['portee' => 5, 'impact' => 4, 'confiance' => 3, 'effort' => 2],
            ['portee' => 3, 'impact' => 5, 'confiance' => 4, 'effort' => 3],
            ['portee' => 4, 'impact' => 3, 'confiance' => 5, 'effort' => 4],
            ['portee' => 2, 'impact' => 2, 'confiance' => 3, 'effort' => 1],
            ['portee' => 5, 'impact' => 5, 'confiance' => 4, 'effort' => 5],
            ['portee' => 1, 'impact' => 1, 'confiance' => 2, 'effort' => 1],
            ['portee' => 4, 'impact' => 4, 'confiance' => 3, 'effort' => 3],
            ['portee' => 3, 'impact' => 3, 'confiance' => 4, 'effort' => 2],
            ['portee' => 5, 'impact' => 5, 'confiance' => 5, 'effort' => 3],
            ['portee' => 2, 'impact' => 3, 'confiance' => 2, 'effort' => 2],
        ];

        foreach ($projects as $index => $project) {
            $evaluationData = $evaluations[$index % count($evaluations)];
            $project->evaluation()->create($evaluationData);
        }
    }
}
