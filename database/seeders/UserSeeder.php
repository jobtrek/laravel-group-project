<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::forceCreate([
            'name' => 'Alice Martin (Proposer)',
            'email' => 'alice@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'proposer',
        ]);

        User::forceCreate([
            'name' => 'Bob Dubois (Direction)',
            'email' => 'bob@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'direction',
        ]);

        User::forceCreate([
            'name' => 'Claire Petit (Récolte)',
            'email' => 'claire@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'recolte_manager',
        ]);

        User::forceCreate([
            'name' => 'David Lefevre (Lead)',
            'email' => 'david@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'project_lead',
        ]);

        User::forceCreate([
            'name' => 'Emma Bernard (Proposer)',
            'email' => 'emma@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'proposer',
        ]);

        User::forceCreate([
            'name' => 'François Leroy (Direction)',
            'email' => 'francois@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'direction',
        ]);

        User::forceCreate([
            'name' => 'Gabrielle Moreau',
            'email' => 'gabrielle@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'proposer',
        ]);

        User::forceCreate([
            'name' => 'Hugo Lambert',
            'email' => 'hugo@foundation.test',
            'password' => Hash::make('password'),
            'role' => 'proposer',
        ]);
    }
}
