<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'view projects',
            'access direction page',
            'approve',
            'deny',
            'review',
        ];

        foreach ($permissions as $permission){
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web']);
        }

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $directionRole = Role::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);

        $userRole->givePermissionTo('view projects');
        $adminRole->givePermissionTo(Permission::all());

        $directionRole->givePermissionTo([
            'approve',
            'deny',
            'review'
        ]);
    }
}
