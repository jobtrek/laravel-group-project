<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectMemberSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $projects = Project::with('members')->get();
        $users = User::with('roles')->get();

        $projects->each(function ($project) use ($users) {
            $users->random(rand(2, 4))->each(function ($user) use ($project) {
                $project->members()->attach($user->id);
            });
        });
    }
}
