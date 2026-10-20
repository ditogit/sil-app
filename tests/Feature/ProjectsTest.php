<?php

namespace Tests\Feature;

use App\Livewire\Projects;
use App\Livewire\ProjectWorkspace;
use App\Livewire\QuickCreateProject;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function manager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('project-manager');

        return $manager;
    }

    public function test_member_only_sees_own_projects(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $mine = Project::factory()->create();
        $mine->members()->attach($member->id);
        Project::factory()->create(); // proyek orang lain

        $response = $this->actingAs($member)->get('/projects');

        $response->assertOk();
        $response->assertSee($mine->name);
        $response->assertDontSee('Tidak ada proyek');
    }

    public function test_manager_can_create_project_with_members(): void
    {
        $manager = $this->manager();
        $member = User::factory()->create();

        Livewire::actingAs($manager)
            ->test(QuickCreateProject::class)
            ->call('openModal')
            ->set('name', 'Portal Baru')
            ->set('description', 'Modernisasi portal.')
            ->set('start_date', today()->toDateString())
            ->set('end_date', today()->addMonths(3)->toDateString())
            ->set('member_ids', [$member->id])
            ->call('save')
            ->assertHasNoErrors();

        $project = Project::where('name', 'Portal Baru')->firstOrFail();

        $this->assertTrue($project->members()->whereKey($manager->id)->exists());
        $this->assertTrue($project->members()->whereKey($member->id)->exists());
    }

    public function test_create_button_dispatches_open_event(): void
    {
        Livewire::actingAs($this->manager())
            ->test(Projects::class)
            ->call('openQuickCreate')
            ->assertDispatched('open-create-project');
    }

    public function test_member_cannot_create_project(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        Livewire::actingAs($member)
            ->test(QuickCreateProject::class)
            ->call('openModal')
            ->set('name', 'Portal Ilegal')
            ->set('start_date', today()->toDateString())
            ->call('save')
            ->assertForbidden();

        $this->assertSame(0, Project::count());
    }

    public function test_manager_can_archive_and_unarchive(): void
    {
        $manager = $this->manager();
        $project = Project::factory()->create(['created_by' => $manager->id]);
        $project->members()->attach($manager->id);

        Livewire::actingAs($manager)
            ->test(ProjectWorkspace::class, ['project' => $project])
            ->call('archive');

        $this->assertSame('archived', $project->fresh()->status);

        Livewire::actingAs($manager)
            ->test(Projects::class)
            ->set('tab', 'archived')
            ->call('unarchive', $project->id);

        $this->assertSame('active', $project->fresh()->status);
    }

    public function test_member_cannot_archive(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project = Project::factory()->create();
        $project->members()->attach($member->id);

        Livewire::actingAs($member)
            ->test(Projects::class)
            ->call('archive', $project->id)
            ->assertForbidden();
    }

    public function test_manager_can_manage_members_and_milestones(): void
    {
        $manager = $this->manager();
        $project = Project::factory()->create(['created_by' => $manager->id]);
        $project->members()->attach($manager->id);

        $member = User::factory()->create();

        $component = Livewire::actingAs($manager)->test(ProjectWorkspace::class, ['project' => $project]);

        $component->set('new_member_id', $member->id)->call('addMember');
        $this->assertTrue($project->members()->whereKey($member->id)->exists());

        $component->call('removeMember', $member->id);
        $this->assertFalse($project->members()->whereKey($member->id)->exists());

        // Pembuat proyek tidak bisa dikeluarkan.
        $component->call('removeMember', $manager->id)->assertHasErrors(['members']);
        $this->assertTrue($project->members()->whereKey($manager->id)->exists());

        $component->set('milestoneTitle', 'Fase 1')->set('milestoneDue', today()->addMonth()->toDateString())->call('addMilestone');
        $milestone = Milestone::where('project_id', $project->id)->firstOrFail();
        $this->assertSame('Fase 1', $milestone->title);

        $component->call('deleteMilestone', $milestone->id);
        $this->assertNull(Milestone::find($milestone->id));
    }

    public function test_task_progress_recalculated_automatically(): void
    {
        $manager = $this->manager();
        $project = Project::factory()->create(['created_by' => $manager->id, 'progress' => 0]);

        Task::factory()->create(['project_id' => $project->id, 'status' => 'todo', 'assignee_id' => $manager->id, 'created_by' => $manager->id]);
        Task::factory()->done()->create(['project_id' => $project->id, 'assignee_id' => $manager->id, 'created_by' => $manager->id]);

        $this->assertSame(50, $project->fresh()->progress);

        Task::where('project_id', $project->id)->where('status', 'todo')->firstOrFail()->update(['status' => 'done']);

        $this->assertSame(100, $project->fresh()->progress);
    }

    public function test_non_member_cannot_open_workspace(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project = Project::factory()->create();

        $this->actingAs($member)->get("/projects/{$project->id}")->assertForbidden();
    }
}
