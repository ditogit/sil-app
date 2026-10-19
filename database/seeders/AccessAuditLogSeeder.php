<?php

namespace Database\Seeders;

use App\Models\AccessAuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccessAuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (! $admin) {
            return;
        }

        User::query()->whereKeyNot($admin->id)->get()->each(function (User $user) use ($admin): void {
            AccessAuditLog::factory()->create([
                'user_id' => $user->id,
                'actor_id' => $admin->id,
                'event' => 'login',
                'description' => "{$user->name} signed in via Google Workspace SSO.",
            ]);
        });

        AccessAuditLog::factory()->create([
            'user_id' => $admin->id,
            'actor_id' => $admin->id,
            'event' => 'permission_changed',
            'description' => 'Updated the QA & Testing Lead role permissions.',
        ]);
    }
}
