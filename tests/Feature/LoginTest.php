<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back');
    }

    public function test_dev_accounts_hidden_outside_local(): void
    {
        $this->seed(RolePermissionSeeder::class);
        User::factory()->create(['email' => 'admin@example.com']);

        // APP_ENV=testing bukan local: panel demo wajib sembunyi.
        $this->get('/login')->assertOk()->assertDontSee('Demo accounts');
    }

    public function test_dev_accounts_shown_on_local(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $member = User::factory()->create(['email' => 'member@example.com']);
        $member->assignRole('team_member');

        $this->app['env'] = 'local';

        $this->get('/login')->assertOk()
            ->assertSee('Demo accounts')
            ->assertSee('member@example.com');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $member = User::factory()->create();
        $member->assignRole('team_member');

        $this->post('/login', ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($member);
    }
}
