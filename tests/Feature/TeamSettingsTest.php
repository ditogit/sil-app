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
    public function member_can_open_the_settings_screen(): void
    {
        $member = $this->userWithRole('team_member', ['name' => 'Rizky Pratama']);

        $this->actingAs($member)->get('/team')
            ->assertOk()
            ->assertSee('Team Settings')
            ->assertSee('Rizky Pratama');
    }

    #[Test]
    public function settings_path_redirects_to_team(): void
    {
        $member = $this->userWithRole('team_member');

        $this->actingAs($member)->get('/settings')->assertRedirect('/team');
    }

    #[Test]
    public function search_filters_members_by_name(): void
    {
        $this->userWithRole('team_member', ['name' => 'Achmad Fauzi']);
        $this->userWithRole('team_member', ['name' => 'Siti Nurhaliza']);

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
        $this->userWithRole('team_member', ['name' => 'Engineer Person']);

        Livewire::actingAs($this->userWithRole('admin'))
            ->test(TeamSettings::class)
            ->set('role', 'viewer')
            ->assertSee('Faculty Viewer')
            ->assertDontSee('Engineer Person');
    }

    #[Test]
    public function manager_can_suspend_a_member_and_it_is_audited(): void
    {
        $target = $this->userWithRole('team_member', ['status' => 'active']);
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
        $target = $this->userWithRole('team_member', ['status' => 'pending_2fa', 'two_factor_confirmed_at' => null]);
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
            ->set('role_name', 'team_member')
            ->set('work_unit_id', (string) $unit->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'new.person@upnyk.ac.id', 'work_unit_id' => $unit->id]);
        $this->assertDatabaseHas('access_audit_logs', ['event' => 'member_added']);
    }

    #[Test]
    public function permission_matrix_saves_role_permissions(): void
    {
        $admin = $this->userWithRole('admin');

        Livewire::actingAs($admin)
            ->test(PermissionMatrix::class)
            ->call('openModal')
            ->set('selected.viewer', ['view projects', 'view tasks'])
            ->call('save')
            ->assertHasNoErrors();

        $viewer = Role::findByName('viewer');
        $this->assertEqualsCanonicalizing(['view projects', 'view tasks'], $viewer->permissions->pluck('name')->all());
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
        $other = $this->userWithRole('team_member');
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
        $target = $this->userWithRole('team_member', ['status' => 'active']);
        $viewer = $this->userWithRole('viewer');

        Livewire::actingAs($viewer)->test(TeamSettings::class)->call('suspend', $target->id);

        $this->assertDatabaseHas('users', ['id' => $target->id, 'status' => 'active']);
    }
}
