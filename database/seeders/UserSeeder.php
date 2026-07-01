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
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->assignRole('user');
        }

        User::factory()->create([
            'name' => 'John Admin',
            'email' => 'admin@mail.fr',
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Phil Direction',
            'email' => 'direction@mail.fr',
        ])->assignRole(['user', 'direction']);

        User::factory()->create([
            'name' => 'Jean Ressources',
            'email' => 'support@mail.fr',
        ])->assignRole(['user', 'resources_manager']);

        User::factory()->create([
            'name' => 'Marc User',
            'email' => 'user@mail.fr',
        ])->assignRole(['user']);
    }
}
