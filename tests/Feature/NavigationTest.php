<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    #[Test]
    public function admin_sees_the_administration_menu(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Administration')
            ->assertSee('Team Members')
            ->assertSee('Reports &amp; Analytics', false);
    }

    #[Test]
    public function project_manager_sees_the_team_menu_but_not_the_audit_actions(): void
    {
        $manager = $this->userWithRole('project-manager');

        $this->actingAs($manager)->get('/dashboard')
            ->assertOk()
            ->assertSee('Administration')
            ->assertSee('Team Members');
    }

    #[Test]
    public function team_member_does_not_see_the_administration_menu(): void
    {
        $member = $this->userWithRole('team-member');

        $this->actingAs($member)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Administration')
            ->assertDontSee('Team Members')
            ->assertDontSee('Reports &amp; Analytics', false);
    }

    #[Test]
    public function viewer_sees_reports_but_not_the_administration_menu(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)->get('/dashboard')
            ->assertOk()
            ->assertSee('Reports &amp; Analytics', false)
            ->assertDontSee('Administration')
            ->assertDontSee('Team Members');
    }

    #[Test]
    public function every_role_sees_the_shared_menu_entries(): void
    {
        foreach (['admin', 'project-manager', 'team-member', 'viewer'] as $role) {
            $user = $this->userWithRole($role);

            $this->actingAs($user)->get('/dashboard')
                ->assertOk()
                ->assertSee('Dashboard')
                ->assertSee('My Tasks')
                ->assertSee('Projects')
                ->assertSee('Task Board')
                ->assertSee('Notifications')
                ->assertSee('Settings');
        }
    }
}
