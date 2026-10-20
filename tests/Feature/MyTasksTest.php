<?php

namespace Tests\Feature;

use App\Livewire\MyTasks;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyTasksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function memberInProject(Project $project): User
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        return $member;
    }

    public function test_page_renders_grouped_tasks(): void
    {
        $project = Project::factory()->create();
        $member = $this->memberInProject($project);

        Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Task telat bayar',
            'status' => 'todo',
            'due_date' => today()->subDay(),
        ]);

        $response = $this->actingAs($member)->get('/tasks');

        $response->assertOk();
        $response->assertSee('My Tasks');
        $response->assertSee('Task telat bayar');
        $response->assertSee('Overdue / Needs Immediate Action');
    }

    public function test_search_filters_by_title(): void
    {
        $project = Project::factory()->create();
        $member = $this->memberInProject($project);

        Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Audit keamanan siber',
            'due_date' => today()->addWeek(),
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Ganti sertifikat SSL',
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->set('q', 'siber')
            ->assertSee('Audit keamanan siber')
            ->assertDontSee('Ganti sertifikat SSL');
    }

    public function test_assignee_can_change_status_directly(): void
    {
        $project = Project::factory()->create();
        $member = $this->memberInProject($project);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'in_progress',
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->call('setStatus', $task->id, 'review');

        $this->assertSame('review', $task->fresh()->status);
    }

    public function test_done_task_cannot_be_reopened_without_permission(): void
    {
        $project = Project::factory()->create();
        $member = $this->memberInProject($project);

        $task = Task::factory()->done()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
        ]);

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->call('setStatus', $task->id, 'todo')
            ->assertForbidden();

        $this->assertSame('done', $task->fresh()->status);
    }

    public function test_manager_can_reopen_done_task(): void
    {
        $project = Project::factory()->create();
        $manager = User::factory()->create();
        $manager->assignRole('project-manager');
        $project->members()->attach($manager->id);

        $task = Task::factory()->done()->create([
            'project_id' => $project->id,
            'assignee_id' => $manager->id,
            'created_by' => $manager->id,
        ]);

        Livewire::actingAs($manager)
            ->test(MyTasks::class)
            ->call('setStatus', $task->id, 'todo');

        $this->assertSame('todo', $task->fresh()->status);
    }

    public function test_member_cannot_delete_task_but_manager_can(): void
    {
        $project = Project::factory()->create();
        $member = $this->memberInProject($project);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->call('deleteTask', $task->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($task);

        $manager = User::factory()->create();
        $manager->assignRole('project-manager');
        $project->members()->attach($manager->id);

        Livewire::actingAs($manager)
            ->test(MyTasks::class)
            ->call('deleteTask', $task->id);

        $this->assertSoftDeleted($task);
    }
}
