<?php

namespace Database\Seeders;

use App\Models\ProjectReview;
use Illuminate\Database\Seeder;

class ProjectReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectReview::factory(10)->create();
    }
}
