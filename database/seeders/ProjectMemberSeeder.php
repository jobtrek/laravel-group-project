<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectMemberSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        $members = $projects->flatMap(fn ($project) =>
        $users->random(rand(2, 4))
            ->map(fn ($user) => ['project_id' => $project->id, 'user_id' => $user->id])
        );

        DB::table('project_members')->insert($members->toArray());
    }
}
