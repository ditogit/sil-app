<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage users', 'manage roles',
            'view projects', 'create projects', 'edit projects',
            'archive projects', 'manage project members',
            'view tasks', 'create tasks', 'assign tasks', 'edit tasks',
            'delete tasks', 'update task status', 'reopen tasks', 'manage all tasks',
            'create comments', 'delete comments',
            'upload attachments', 'delete attachments',
            'view dashboard', 'view reports', 'export reports',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web'])->syncPermissions([
            'view projects', 'create projects', 'edit projects',
            'archive projects', 'manage project members',
            'view tasks', 'create tasks', 'assign tasks', 'edit tasks',
            'delete tasks', 'update task status', 'reopen tasks', 'manage all tasks',
            'create comments', 'delete comments',
            'upload attachments', 'delete attachments',
            'view dashboard', 'view reports', 'export reports',
        ]);

        Role::firstOrCreate(['name' => 'team_member', 'guard_name' => 'web'])->syncPermissions([
            'view projects',
            'view tasks', 'create tasks', 'edit tasks', 'update task status',
            'create comments',
            'upload attachments', 'delete attachments',
            'view dashboard',
        ]);

        Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web'])->syncPermissions([
            'view projects', 'view tasks',
            'create comments',
            'view dashboard', 'view reports',
        ]);
    }
}
