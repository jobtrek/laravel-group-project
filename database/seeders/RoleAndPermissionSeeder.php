<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'approve',
            'deny',
            'review',
            'evaluate projects',
            'manage everything',
            'edit project',
            'add resources',
            'send to direction',
            'archive projects',
            'assign team',
            'launch project',
            'complete project',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'collaborateur', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $directionRole = Role::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);
        $chefDeProjet = Role::firstOrCreate(['name' => 'chef_de_projet', 'guard_name' => 'web']);
        $projectManager = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web']);
        $recolteManager = Role::firstOrCreate(['name' => 'recolte_manager', 'guard_name' => 'web']);

        $adminRole->givePermissionTo([
            'manage everything',
        ]);
        $directionRole->givePermissionTo([
            'approve',
            'deny',
            'review',
            'evaluate projects',
            'archive projects',
            'send to direction',
        ]);
        $chefDeProjet->givePermissionTo([
            'edit project',
            'launch project',
            'complete project',
        ]);
        $recolteManager->givePermissionTo([
            'add resources',
            'assign team',
        ]);

        $projectManager->givePermissionTo([
            'archive projects',
            'send to direction',
        ]);
    }
}
