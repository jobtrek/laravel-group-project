<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Gate;
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web']);
        }

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $directionRole = Role::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);
        $projectLeader = Role::firstOrCreate(['name' => 'project_leader', 'guard_name' => 'web']);
        $projectManager = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web']);
        $resourcesSupport = Role::firstOrCreate(['name' => 'resources_support', 'guard_name' => 'web']);

        Gate::before(function ($user, $ability) {
            return $user->hasPermissionTo('manage everything') ? true : null;
        });
        $directionRole->givePermissionTo([
            'approve',
            'deny',
            'review',
        ]);
    }
}
