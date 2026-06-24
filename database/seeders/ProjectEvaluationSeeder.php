<?php

namespace Database\Seeders;

use App\Models\ProjectEvaluation;
use Illuminate\Database\Seeder;

class ProjectEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectEvaluation::factory(10)->create();
    }
}
