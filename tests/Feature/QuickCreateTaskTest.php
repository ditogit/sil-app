<?php

namespace Tests\Feature;

use App\Livewire\MyTasks;
use App\Livewire\QuickCreateTask;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuickCreateTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_member_can_quick_create_personal_task(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project = Project::factory()->create();
        $project->members()->attach($member->id);

        Livewire::actingAs($member)
            ->test(QuickCreateTask::class)
            ->call('openModal')
            ->set('project_id', $project->id)
            ->set('title', 'Task kilat pribadi')
            ->set('due_date', today()->addDays(2)->toDateString())
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('task-created');

        $task = Task::where('title', 'Task kilat pribadi')->firstOrFail();

        $this->assertSame($member->id, $task->assignee_id);
        $this->assertSame($member->id, $task->created_by);
        $this->assertSame('todo', $task->status);
    }

    public function test_quick_create_appears_in_my_tasks(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project = Project::factory()->create();
        $project->members()->attach($member->id);

        Livewire::actingAs($member)
            ->test(QuickCreateTask::class)
            ->call('openModal')
            ->set('project_id', $project->id)
            ->set('title', 'Task muncul di list')
            ->set('due_date', today()->addDays(2)->toDateString())
            ->call('save');

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->assertSee('Task muncul di list');
    }

    public function test_viewer_cannot_quick_create(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        Livewire::actingAs($viewer)
            ->test(QuickCreateTask::class)
            ->call('openModal')
            ->set('title', 'Task viewer')
            ->call('save')
            ->assertForbidden();
    }

    public function test_parent_button_dispatches_open_event(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->call('openQuickCreate')
            ->assertDispatched('open-quick-create');
    }

    public function test_past_due_date_rejected(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project = Project::factory()->create();

        Livewire::actingAs($member)
            ->test(QuickCreateTask::class)
            ->call('openModal')
            ->set('project_id', $project->id)
            ->set('title', 'Task basi')
            ->set('due_date', today()->subDay()->toDateString())
            ->call('save')
            ->assertHasErrors(['due_date']);
    }
}
