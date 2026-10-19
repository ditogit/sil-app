<?php

namespace Tests\Feature;

use App\Livewire\CreateTask;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function member(): User
    {
        $member = User::factory()->create();
        $member->assignRole('team_member');

        return $member;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/tasks/create')->assertRedirect('/login');
    }

    public function test_viewer_without_permission_gets_forbidden(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        $this->actingAs($viewer)->get('/tasks/create')->assertForbidden();
    }

    public function test_member_can_create_task_with_tags_and_attachment(): void
    {
        Storage::fake('public');

        $member = $this->member();
        $project = Project::factory()->create(['created_by' => $member->id]);
        $milestone = Milestone::factory()->create(['project_id' => $project->id]);

        $file = UploadedFile::fake()->create('spesifikasi.pdf', 100, 'application/pdf');

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('milestone_id', $milestone->id)
            ->set('title', 'Integrasi payment gateway')
            ->set('description', 'Spesifikasi callback VA.')
            ->set('assignee_id', $member->id)
            ->set('priority', 'critical')
            ->set('due_date', today()->addDays(4)->toDateString())
            ->set('due_time', '16:00')
            ->set('story_points', 5)
            ->set('estimate_hours', 16)
            ->set('tagInput', 'Backend')
            ->call('addTag')
            ->set('attachments', [$file])
            ->call('save')
            ->assertRedirect(route('tasks.mine'));

        $task = Task::where('title', 'Integrasi payment gateway')->firstOrFail();

        $this->assertSame('critical', $task->priority);
        $this->assertSame(['Backend'], $task->tags);
        $this->assertSame(5, $task->story_points);
        $this->assertSame($member->id, $task->created_by);
        $this->assertCount(1, $task->attachments);
        $this->assertSame('spesifikasi.pdf', $task->attachments->first()->file_name);
        Storage::disk('public')->assertExists($task->attachments->first()->file_path);
    }

    public function test_due_date_in_the_past_is_rejected(): void
    {
        $member = $this->member();
        $project = Project::factory()->create(['created_by' => $member->id]);

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Task basi')
            ->set('assignee_id', $member->id)
            ->set('due_date', today()->subDay()->toDateString())
            ->call('save')
            ->assertHasErrors(['due_date']);

        $this->assertSame(0, Task::count());
    }

    public function test_member_cannot_assign_to_someone_else(): void
    {
        $member = $this->member();
        $other = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $member->id]);

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Task orang lain')
            ->set('assignee_id', $other->id)
            ->set('due_date', today()->addWeek()->toDateString())
            ->call('save')
            ->assertHasErrors(['assignee_id']);

        $this->assertSame(0, Task::count());
    }

    public function test_manager_can_assign_to_someone_else(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('project_manager');

        $member = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $manager->id]);

        Livewire::actingAs($manager)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Task delegasi')
            ->set('assignee_id', $member->id)
            ->set('due_date', today()->addWeek()->toDateString())
            ->call('save')
            ->assertRedirect(route('tasks.mine'));

        $this->assertSame($member->id, Task::firstOrFail()->assignee_id);
    }

    public function test_milestone_from_another_project_is_rejected(): void
    {
        $member = $this->member();
        $project = Project::factory()->create(['created_by' => $member->id]);
        $otherMilestone = Milestone::factory()->create();

        Livewire::actingAs($member)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('milestone_id', $otherMilestone->id)
            ->set('title', 'Task salah milestone')
            ->set('assignee_id', $member->id)
            ->set('due_date', today()->addWeek()->toDateString())
            ->call('save')
            ->assertHasErrors(['milestone_id']);
    }
}
