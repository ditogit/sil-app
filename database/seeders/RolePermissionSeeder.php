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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Project
            'project.view', 'project.create', 'project.update', 'project.archive', 'project.manage-members',
            // Task
            'task.view', 'task.create', 'task.assign', 'task.update', 'task.update-status', 'task.delete', 'task.reopen',
            // Sub-task
            'subtask.create', 'subtask.update',
            // Comment
            'comment.create', 'comment.delete-any',
            // Attachment
            'attachment.upload', 'attachment.delete',
            // Dashboard & Laporan
            'dashboard.view', 'report.view', 'report.export',
            // Sistem
            'user.manage', 'user.invite', 'role.manage', 'system.configure',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Admin: semua permission
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        // Project Manager
        Role::firstOrCreate(['name' => 'project-manager', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view', 'project.create', 'project.update', 'project.archive', 'project.manage-members',
                'task.view', 'task.create', 'task.assign', 'task.update', 'task.update-status',
                'task.delete', 'task.reopen',
                'subtask.create', 'subtask.update',
                'comment.create', 'comment.delete-any',
                'attachment.upload', 'attachment.delete',
                'dashboard.view', 'report.view', 'report.export',
                'user.invite',
            ]);

        // Team Member (TIDAK punya task.assign)
        Role::firstOrCreate(['name' => 'team-member', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view',
                'task.view', 'task.create', 'task.update', 'task.update-status',
                'subtask.create', 'subtask.update',
                'comment.create',
                'attachment.upload', 'attachment.delete',
                'dashboard.view',
            ]);

        // Viewer (boleh report.view & report.export)
        Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view', 'task.view',
                'comment.create',
                'dashboard.view', 'report.view', 'report.export',
            ]);
    }
}
