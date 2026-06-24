<?php

namespace Database\Seeders;

use App\Models\ProjectMember;
use Illuminate\Database\Seeder;

class ProjectMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectMember::factory(10)->create();
    }
}
