<?php

namespace Tests\Feature;

use App\Livewire\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_board_renders_columns_with_project_tasks(): void
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Task papan kanban',
            'status' => 'in_progress',
            'due_date' => today()->addWeek(),
        ]);

        $response = $this->actingAs($member)->get('/board');

        $response->assertOk();
        $response->assertSee('Task Board');
        $response->assertSee('Task papan kanban');
        $response->assertSee('In Progress');
    }

    public function test_assignee_can_drag_task_to_another_column(): void
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'todo',
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($member)
            ->test(Board::class)
            ->call('moveTask', $task->id, 'review');

        $this->assertSame('review', $task->fresh()->status);
    }

    public function test_stranger_cannot_move_task(): void
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'todo',
            'due_date' => today()->addWeek(),
        ]);

        $outsider = User::factory()->create();
        $outsider->assignRole('team-member');

        Livewire::actingAs($outsider)
            ->test(Board::class)
            ->call('moveTask', $task->id, 'done')
            ->assertForbidden();

        $this->assertSame('todo', $task->fresh()->status);
    }

    public function test_board_scoped_to_project_query_param(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $projectA = Project::factory()->create();
        $projectA->members()->attach($member->id);
        $projectB = Project::factory()->create();
        $projectB->members()->attach($member->id);

        Task::factory()->create([
            'project_id' => $projectA->id, 'assignee_id' => $member->id, 'created_by' => $member->id,
            'title' => 'Task proyek A', 'due_date' => today()->addWeek(),
        ]);
        Task::factory()->create([
            'project_id' => $projectB->id, 'assignee_id' => $member->id, 'created_by' => $member->id,
            'title' => 'Task proyek B', 'due_date' => today()->addWeek(),
        ]);

        $response = $this->actingAs($member)->get("/board?project={$projectA->id}");

        $response->assertOk();
        $response->assertSee('Task proyek A');
        $response->assertDontSee('Task proyek B');
    }
}
