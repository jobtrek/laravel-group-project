<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });

        User::factory()->create([
            'name' => 'John Admin',
            'email' => 'admin@mail.fr',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Phil Direction',
            'email' => 'direction@mail.fr',
            'password' => bcrypt('password'),
        ])->assignRole(['user', 'direction']);
    }
}
