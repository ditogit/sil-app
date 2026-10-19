<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        $manager = User::firstOrCreate(
            ['email' => 'pm@example.com'],
            ['name' => 'Project Manager', 'password' => Hash::make('password')]
        );
        $manager->syncRoles(['project_manager']);

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            ['name' => 'Team Member', 'password' => Hash::make('password')]
        );
        $member->syncRoles(['team_member']);

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Viewer', 'password' => Hash::make('password')]
        );
        $viewer->syncRoles(['viewer']);
    }
}
