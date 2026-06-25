<?php

namespace Database\Seeders;

use App\Models\PhaseResource;
use Illuminate\Database\Seeder;

class PhaseResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PhaseResource::factory()->count(10)->create();
    }
}
