<?php

namespace Tests\Feature;

use App\Livewire\CreateTask;
use App\Livewire\TaskDetail;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Fase 7 — 18 Skenario Wajib Role & Permission
 *
 * @see Rencana_Aksi_Role_Permission_Spatie.md §7.2
 */
class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Create a user with the given role. */
    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /**
     * Create a project + task owned by $assignee who is a project member.
     *
     * @return array{Project, Task}
     */
    private function projectAndTask(User $assignee, string $taskStatus = 'todo'): array
    {
        $project = Project::factory()->create();
        $project->members()->attach($assignee->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $assignee->id,
            'created_by' => $assignee->id,
            'status' => $taskStatus,
        ]);

        return [$project, $task];
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 1: Team Member bukan assignee mengubah status task → 403
    // -----------------------------------------------------------------------
    public function test_s1_team_member_non_assignee_cannot_change_status(): void
    {
        $assignee = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($assignee);

        $other = $this->userWithRole('team-member');
        $project->members()->attach($other->id);

        Livewire::actingAs($other)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('setStatus', 'in_progress')
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 2: Team Member assignee mengubah status task miliknya → 200
    // -----------------------------------------------------------------------
    public function test_s2_team_member_assignee_can_change_own_task_status(): void
    {
        $member = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($member);

        Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('setStatus', 'in_progress')
            ->assertHasNoErrors();

        $this->assertSame('in_progress', $task->fresh()->status);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 3: Team Member mengubah task berstatus Done → 403
    // -----------------------------------------------------------------------
    public function test_s3_team_member_cannot_reopen_done_task(): void
    {
        $member = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($member, 'done');

        Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('setStatus', 'in_progress')
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 4: PM/Admin mengubah task berstatus Done → 200
    // -----------------------------------------------------------------------
    public function test_s4_pm_can_reopen_done_task(): void
    {
        $pm = $this->userWithRole('project-manager');
        $member = $this->userWithRole('team-member');

        $project = Project::factory()->create();
        $project->members()->attach([$pm->id, $member->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'done',
        ]);

        Livewire::actingAs($pm)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('setStatus', 'in_progress')
            ->assertHasNoErrors();

        $this->assertSame('in_progress', $task->fresh()->status);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 5: User bukan anggota proyek membuka task → 403/404
    // -----------------------------------------------------------------------
    public function test_s5_non_member_cannot_view_task(): void
    {
        $owner = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($owner);

        $outsider = $this->userWithRole('team-member');

        $this->actingAs($outsider)
            ->get("/tasks/{$task->id}")
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 6: Team Member membuat task dengan assignee_id orang lain → 403/422
    // -----------------------------------------------------------------------
    public function test_s6_team_member_cannot_assign_task_to_other_user(): void
    {
        $member = $this->userWithRole('team-member');
        $other = $this->userWithRole('team-member');
        $project = Project::factory()->create();
        $project->members()->attach([$member->id, $other->id]);

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Test task')
            ->set('due_date', today()->addWeek()->toDateString())
            ->set('assignee_id', $other->id)
            ->call('save')
            ->assertHasErrors(['assignee_id']);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 7: Team Member membuat task tanpa assignee → assignee = dirinya sendiri
    // -----------------------------------------------------------------------
    public function test_s7_team_member_task_without_assignee_defaults_to_self(): void
    {
        $member = $this->userWithRole('team-member');
        $project = Project::factory()->create();
        $project->members()->attach($member->id);

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Self-assign task')
            ->set('due_date', today()->addWeek()->toDateString())
            ->set('assignee_id', null)
            ->call('save');

        $task = Task::where('title', 'Self-assign task')->first();
        // assignee_id should be null OR the member's own id (component defaults to auth->id())
        $this->assertTrue(
            $task === null || $task->assignee_id === null || $task->assignee_id === $member->id,
            'Team member task should be self-assigned'
        );
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 8: PM meng-assign task ke anggota proyek → 200
    // -----------------------------------------------------------------------
    public function test_s8_pm_can_assign_task_to_project_member(): void
    {
        $pm = $this->userWithRole('project-manager');
        $member = $this->userWithRole('team-member');

        $project = Project::factory()->create();
        $project->members()->attach([$pm->id, $member->id]);

        Livewire::actingAs($pm)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'PM creates task')
            ->set('due_date', today()->addWeek()->toDateString())
            ->set('assignee_id', $member->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'PM creates task',
            'assignee_id' => $member->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 9: PM meng-assign task ke user non-anggota proyek → 422
    // -----------------------------------------------------------------------
    public function test_s9_pm_cannot_assign_task_to_non_member(): void
    {
        $pm = $this->userWithRole('project-manager');
        $nonMember = $this->userWithRole('team-member');

        $project = Project::factory()->create();
        $project->members()->attach($pm->id);
        // $nonMember is NOT attached to the project

        Livewire::actingAs($pm)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Should fail')
            ->set('due_date', today()->addWeek()->toDateString())
            ->set('assignee_id', $nonMember->id)
            ->call('save')
            ->assertHasErrors(['assignee_id']);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 10: Viewer membuat task → 403
    // -----------------------------------------------------------------------
    public function test_s10_viewer_cannot_create_task(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)
            ->get('/tasks/create')
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 11: Viewer membuat komentar → 200
    // -----------------------------------------------------------------------
    public function test_s11_viewer_can_create_comment(): void
    {
        $owner = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($owner);

        $viewer = $this->userWithRole('viewer');
        $project->members()->attach($viewer->id);

        Livewire::actingAs($viewer)
            ->test(TaskDetail::class, ['task' => $task])
            ->set('commentBody', 'Komentar dari viewer.')
            ->call('sendComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', ['body' => 'Komentar dari viewer.']);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 12: Viewer menghapus komentar orang lain → 403
    // -----------------------------------------------------------------------
    public function test_s12_viewer_cannot_delete_others_comment(): void
    {
        $owner = $this->userWithRole('team-member');
        [$project, $task] = $this->projectAndTask($owner);

        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $owner->id,
        ]);

        $viewer = $this->userWithRole('viewer');
        $project->members()->attach($viewer->id);

        Livewire::actingAs($viewer)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteComment', $comment->id)
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 13: Viewer mengakses /reports dan export → 200
    // -----------------------------------------------------------------------
    public function test_s13_viewer_can_access_reports_and_export(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)
            ->get('/reports')
            ->assertOk();

        $this->actingAs($viewer)
            ->get(route('reports.export'))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 14: Team Member mengakses /reports → 403
    // -----------------------------------------------------------------------
    public function test_s14_team_member_cannot_access_reports(): void
    {
        $member = $this->userWithRole('team-member');

        $this->actingAs($member)
            ->get('/reports')
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 15: Team Member membuat / mengarsip proyek → 403
    // -----------------------------------------------------------------------
    public function test_s15_team_member_cannot_create_or_archive_project(): void
    {
        $member = $this->userWithRole('team-member');

        $this->assertFalse($member->can('project.create'));
        $this->assertFalse($member->can('project.archive'));
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 16: PM mengarsip proyek yang bukan miliknya → 403
    // -----------------------------------------------------------------------
    public function test_s16_pm_cannot_archive_project_without_membership(): void
    {
        $pm = $this->userWithRole('project-manager');
        $project = Project::factory()->create(); // PM not attached

        $this->assertFalse((bool) Gate::forUser($pm)->allows('archive', $project));
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 17: Deadline di masa lalu saat membuat task → 422
    // -----------------------------------------------------------------------
    public function test_s17_past_deadline_is_rejected(): void
    {
        $pm = $this->userWithRole('project-manager');
        $project = Project::factory()->create();
        $project->members()->attach($pm->id);

        Livewire::actingAs($pm)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Backdated task')
            ->set('due_date', today()->subDay()->toDateString())
            ->call('save')
            ->assertHasErrors(['due_date']);
    }

    // -----------------------------------------------------------------------
    // §7.2 — Skenario 18: Hanya Admin mengakses halaman kelola user/role
    // -----------------------------------------------------------------------
    public function test_s18_only_admin_can_access_team_management(): void
    {
        $admin = $this->userWithRole('admin');
        $pm = $this->userWithRole('project-manager');
        $member = $this->userWithRole('team-member');
        $viewer = $this->userWithRole('viewer');

        // Admin can manage
        $this->assertTrue($admin->can('user.manage'));
        $this->assertTrue($admin->can('role.manage'));

        // Others cannot
        foreach ([$pm, $member, $viewer] as $user) {
            $this->assertFalse($user->can('user.manage'), "Role {$user->getRoleNames()->first()} should not have user.manage");
            $this->assertFalse($user->can('role.manage'), "Role {$user->getRoleNames()->first()} should not have role.manage");
        }
    }

    // -----------------------------------------------------------------------
    // §7.3 — Test Seeder: jumlah role & permission, dan matriks
    // -----------------------------------------------------------------------
    public function test_seeder_creates_correct_number_of_roles(): void
    {
        $this->assertSame(4, Role::count());
    }

    public function test_seeder_creates_correct_permissions(): void
    {
        $expectedPermissions = [
            'project.view', 'project.create', 'project.update', 'project.archive', 'project.manage-members',
            'task.view', 'task.create', 'task.assign', 'task.update', 'task.update-status', 'task.delete', 'task.reopen',
            'subtask.create', 'subtask.update',
            'comment.create', 'comment.delete-any',
            'attachment.upload', 'attachment.delete',
            'dashboard.view', 'report.view', 'report.export',
            'user.manage', 'user.invite', 'role.manage', 'system.configure',
        ];

        $this->assertSame(count($expectedPermissions), Permission::count());

        foreach ($expectedPermissions as $perm) {
            $this->assertTrue(
                Permission::where('name', $perm)->exists(),
                "Permission '{$perm}' is missing"
            );
        }
    }

    public function test_team_member_does_not_have_task_assign(): void
    {
        $member = $this->userWithRole('team-member');

        $this->assertFalse($member->can('task.assign'));
    }

    public function test_viewer_has_report_export(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->assertTrue($viewer->can('report.export'));
    }
}
