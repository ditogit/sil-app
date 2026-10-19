<?php

namespace Database\Factories;

use App\Models\AccessAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessAuditLog>
 */
class AccessAuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'actor_id' => User::factory(),
            'event' => fake()->randomElement([
                'login', 'logout', 'role_assigned', 'role_revoked',
                'suspended', 'reactivated', 'permission_changed', '2fa_enabled',
            ]),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
