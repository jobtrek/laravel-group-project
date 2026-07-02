<?php

namespace Database\Seeders;

use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            Project::factory(123)
                ->has(
                    ProjectPhase::factory()
                        ->count(3)
                        ->has(PhaseResource::factory()->count(2), 'resources'),
                    'phases'
                )
                ->create();
        });
    }
}
