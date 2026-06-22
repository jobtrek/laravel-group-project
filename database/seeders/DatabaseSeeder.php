<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CacheTableSeeder::class,
            JobsTableSeeder::class,
            UserSeeder::class,
            ProjectsSeeder::class,
            ProjectMemberSeeder::class,
            ProjectPhaseSeeder::class,
            PhaseResourceSeeder::class,
            ProjectEvaluationSeeder::class,
            ProjectReviewSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
