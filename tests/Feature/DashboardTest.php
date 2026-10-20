<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_renders_with_my_tasks(): void
    {
        $member = User::factory()->create(['name' => 'Anggota Tim']);
        $member->assignRole('team-member');

        $project = Project::factory()->create(['created_by' => $member->id]);
        $project->members()->attach($member->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Perbaiki bug login',
            'status' => 'todo',
        ]);

        Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $member->id,
        ]);

        $response = $this->actingAs($member)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('My Tasks');
        $response->assertSee('Perbaiki bug login');
        $response->assertSee('Total Active Tasks');
        $response->assertSee('commented on');
    }

    public function test_assignee_can_advance_own_task_status(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $task = Task::factory()->create([
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'todo',
        ]);
        $task->project->members()->attach($member->id);

        Livewire::actingAs($member)
            ->test(Dashboard::class)
            ->call('advanceStatus', $task->id);

        $this->assertSame('in_progress', $task->fresh()->status);
    }

    public function test_stranger_cannot_advance_others_task_status(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $other = User::factory()->create();
        $other->assignRole('team-member');

        $task = Task::factory()->create([
            'assignee_id' => $other->id,
            'created_by' => $other->id,
            'status' => 'todo',
        ]);

        Livewire::actingAs($member)
            ->test(Dashboard::class)
            ->call('advanceStatus', $task->id)
            ->assertForbidden();

        $this->assertSame('todo', $task->fresh()->status);
    }
}
