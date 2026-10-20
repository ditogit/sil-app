<?php

namespace Tests\Feature;

use App\Livewire\AddTeamMember;
use App\Livewire\PermissionMatrix;
use App\Livewire\TeamSettings;
use App\Models\AccessAuditLog;
use App\Models\User;
use App\Models\WorkUnit;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    #[Test]
    public function admin_can_open_the_team_settings_screen(): void
    {
        $admin = $this->userWithRole('admin', ['name' => 'Rizky Pratama']);

        $this->actingAs($admin)->get('/team')
            ->assertOk()
            ->assertSee('Team Settings')
            ->assertSee('Rizky Pratama');
    }

    #[Test]
    public function member_cannot_open_the_team_settings_screen(): void
    {
        $member = $this->userWithRole('team-member');

        $this->actingAs($member)->get('/team')->assertForbidden();
    }

    #[Test]
    public function viewer_cannot_open_the_team_settings_screen(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)->get('/team')->assertForbidden();
    }

    #[Test]
    public function search_filters_members_by_name(): void
    {
        $this->userWithRole('team-member', ['name' => 'Achmad Fauzi']);
        $this->userWithRole('team-member', ['name' => 'Siti Nurhaliza']);

        Livewire::actingAs($this->userWithRole('admin'))
            ->test(TeamSettings::class)
            ->set('q', 'Achmad')
            ->assertSee('Achmad Fauzi')
            ->assertDontSee('Siti Nurhaliza');
    }

    #[Test]
    public function role_filter_only_shows_matching_members(): void
    {
        $this->userWithRole('viewer', ['name' => 'Faculty Viewer']);
        $this->userWithRole('team-member', ['name' => 'Engineer Person']);

        Livewire::actingAs($this->userWithRole('admin'))
            ->test(TeamSettings::class)
            ->set('role', 'viewer')
            ->assertSee('Faculty Viewer')
            ->assertDontSee('Engineer Person');
    }

    #[Test]
    public function manager_can_suspend_a_member_and_it_is_audited(): void
    {
        $target = $this->userWithRole('team-member', ['status' => 'active']);
        $manager = $this->userWithRole('admin');

        Livewire::actingAs($manager)
            ->test(TeamSettings::class)
            ->call('suspend', $target->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'status' => 'suspended']);
        $this->assertDatabaseHas('access_audit_logs', ['user_id' => $target->id, 'event' => 'suspended']);
    }

    #[Test]
    public function manager_can_approve_a_pending_member(): void
    {
        $target = $this->userWithRole('team-member', ['status' => 'pending_2fa', 'two_factor_confirmed_at' => null]);
        $manager = $this->userWithRole('admin');

        Livewire::actingAs($manager)
            ->test(TeamSettings::class)
            ->call('approve', $target->id)
            ->assertHasNoErrors();

        $target->refresh();
        $this->assertSame('active', $target->status);
        $this->assertTrue($target->hasTwoFactor());
    }

    #[Test]
    public function manager_can_add_a_team_member(): void
    {
        $manager = $this->userWithRole('admin');
        $unit = WorkUnit::factory()->create();

        Livewire::actingAs($manager)
            ->test(AddTeamMember::class)
            ->call('openModal')
            ->set('name', 'New Person')
            ->set('email', 'new.person@upnyk.ac.id')
            ->set('password', 'secret-password')
            ->set('role_name', 'team-member')
            ->set('work_unit_id', (string) $unit->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'new.person@upnyk.ac.id', 'work_unit_id' => $unit->id]);
        $this->assertDatabaseHas('access_audit_logs', ['event' => 'member_added']);
    }

    #[Test]
    public function project_manager_can_open_the_team_screen_and_see_the_add_member_action(): void
    {
        $manager = $this->userWithRole('project-manager');

        $this->actingAs($manager)->get('/team')
            ->assertOk()
            ->assertSee('Add Team Member')
            ->assertDontSee('Export Access Log')
            ->assertDontSee('Security Audit Log');
    }

    #[Test]
    public function project_manager_can_add_a_team_member(): void
    {
        $manager = $this->userWithRole('project-manager');

        Livewire::actingAs($manager)
            ->test(AddTeamMember::class)
            ->call('openModal')
            ->set('name', 'New Engineer')
            ->set('email', 'new.engineer@upnyk.ac.id')
            ->set('password', 'secret-password')
            ->set('role_name', 'team-member')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'new.engineer@upnyk.ac.id']);

        $created = User::where('email', 'new.engineer@upnyk.ac.id')->firstOrFail();

        $this->assertTrue($created->hasRole('team-member'));
    }

    #[Test]
    public function project_manager_cannot_assign_the_admin_role(): void
    {
        $manager = $this->userWithRole('project-manager');

        Livewire::actingAs($manager)
            ->test(AddTeamMember::class)
            ->call('openModal')
            ->set('name', 'Sneaky Admin')
            ->set('email', 'sneaky.admin@upnyk.ac.id')
            ->set('password', 'secret-password')
            ->set('role_name', 'admin')
            ->call('save')
            ->assertHasErrors('role_name');

        $this->assertDatabaseMissing('users', ['email' => 'sneaky.admin@upnyk.ac.id']);
    }

    #[Test]
    public function project_manager_cannot_suspend_a_member(): void
    {
        $target = $this->userWithRole('team-member', ['status' => 'active']);
        $manager = $this->userWithRole('project-manager');

        Livewire::actingAs($manager)
            ->test(TeamSettings::class)
            ->call('suspend', $target->id)
            ->assertForbidden();

        $this->assertSame('active', $target->refresh()->status);
    }

    #[Test]
    public function project_manager_cannot_change_member_permissions(): void
    {
        $target = $this->userWithRole('team-member');
        $manager = $this->userWithRole('project-manager');

        Livewire::actingAs($manager)
            ->test(TeamSettings::class)
            ->call('openManageMember', $target->id)
            ->assertForbidden();
    }

    #[Test]
    public function project_manager_cannot_open_the_audit_log_tab(): void
    {
        $manager = $this->userWithRole('project-manager');

        Livewire::actingAs($manager)
            ->test(TeamSettings::class)
            ->call('setTab', 'audit')
            ->assertForbidden();
    }

    #[Test]
    public function permission_matrix_saves_role_permissions(): void
    {
        $admin = $this->userWithRole('admin');

        Livewire::actingAs($admin)
            ->test(PermissionMatrix::class)
            ->call('openModal')
            ->set('selected.viewer', ['project.view', 'task.view'])
            ->call('save')
            ->assertHasNoErrors();

        $viewer = Role::findByName('viewer');
        $this->assertEqualsCanonicalizing(['project.view', 'task.view'], $viewer->permissions->pluck('name')->all());
    }

    #[Test]
    public function viewer_cannot_export_the_access_log(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)->get('/team/audit/export')->assertForbidden();
    }

    #[Test]
    public function manager_can_export_the_access_log_as_csv(): void
    {
        $manager = $this->userWithRole('admin');
        $other = $this->userWithRole('team-member');
        AccessAuditLog::factory()->create([
            'user_id' => $other->id,
            'actor_id' => $manager->id,
            'event' => 'login',
        ]);

        $this->actingAs($manager)->get('/team/audit/export')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function viewer_cannot_manage_members_through_the_component(): void
    {
        $target = $this->userWithRole('team-member', ['status' => 'active']);
        $viewer = $this->userWithRole('viewer');

        Livewire::actingAs($viewer)->test(TeamSettings::class)->call('suspend', $target->id);

        $this->assertDatabaseHas('users', ['id' => $target->id, 'status' => 'active']);
    }
}
