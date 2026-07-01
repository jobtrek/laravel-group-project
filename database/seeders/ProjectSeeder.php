<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\PhaseResource;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(123)
            ->has(
                ProjectPhase::factory()
                    ->count(3)
                    ->has(PhaseResource::factory()->count(2), 'resources'),
                'phases'
            )
            ->create();
    }
}
