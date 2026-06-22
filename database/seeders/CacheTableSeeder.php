<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cache')->updateOrInsert(
            ['key' => 'sample_key'],
            ['value' => 'sample_value', 'expiration' => now()->addDay()->timestamp],
        );

        DB::table('cache_locks')->updateOrInsert(
            ['key' => 'sample_lock'],
            ['owner' => 'seeder', 'expiration' => now()->addHour()->timestamp],
        );
    }
}
