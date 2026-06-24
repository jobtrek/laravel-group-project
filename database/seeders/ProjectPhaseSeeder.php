<?php

namespace Database\Seeders;

use App\Models\ProjectPhase;
use Illuminate\Database\Seeder;

class ProjectPhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectPhase::factory(10)->create();
    }
}
