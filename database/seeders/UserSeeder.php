<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice Martin (Proposer)',
            'email' => 'alice@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Bob Dubois (Direction)',
            'email' => 'bob@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Claire Petit (Récolte)',
            'email' => 'claire@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'David Lefevre (Lead)',
            'email' => 'david@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Emma Bernard (Proposer)',
            'email' => 'emma@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'François Leroy (Direction)',
            'email' => 'francois@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Gabrielle Moreau',
            'email' => 'gabrielle@foundation.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Hugo Lambert',
            'email' => 'hugo@foundation.test',
            'password' => Hash::make('password'),
        ]);
    }
}
