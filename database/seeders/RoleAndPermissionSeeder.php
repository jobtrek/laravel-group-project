<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
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
            'restore',
            'assign team',
            'launch project',
            'complete project',
            'create project',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web']);
        }

        $collaborateur = Role::firstOrCreate(['name' => RoleEnum::Collaborateur->value, 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::Admin->value, 'guard_name' => 'web']);
        $directionRole = Role::firstOrCreate(['name' => RoleEnum::Direction->value, 'guard_name' => 'web']);
        $chefDeProjet = Role::firstOrCreate(['name' => RoleEnum::ChefDeProjet->value, 'guard_name' => 'web']);
        $projectManager = Role::firstOrCreate(['name' => RoleEnum::ProjectManager->value, 'guard_name' => 'web']);
        $recolteManager = Role::firstOrCreate(['name' => RoleEnum::RecolteManager->value, 'guard_name' => 'web']);

        $collaborateur->givePermissionTo('create project');

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
            'restore',
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
