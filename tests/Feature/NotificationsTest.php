<?php

namespace Tests\Feature;

use App\Livewire\CreateTask;
use App\Livewire\MyTasks;
use App\Livewire\NotificationCenter;
use App\Livewire\TaskDetail;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_assigning_task_notifies_assignee(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('project-manager');

        $member = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $manager->id]);
        $project->members()->attach([$manager->id, $member->id]);

        Livewire::actingAs($manager)
            ->test(CreateTask::class)
            ->set('project_id', $project->id)
            ->set('title', 'Task notifikasi')
            ->set('assignee_id', $member->id)
            ->set('due_date', today()->addWeek()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, $member->notifications()->count());
        $this->assertSame('assigned', $member->notifications()->first()->data['kind']);
    }

    public function test_status_change_notifies_followers_except_actor(): void
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        $creator = User::factory()->create();
        $creator->assignRole('team-member');
        $project->members()->attach($creator->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $creator->id,
            'status' => 'todo',
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($member)
            ->test(MyTasks::class)
            ->call('setStatus', $task->id, 'in_progress');

        $this->assertSame(1, $creator->notifications()->count());
        $this->assertSame(0, $member->notifications()->count());
    }

    public function test_comment_notifies_assignee(): void
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        $author = User::factory()->create();
        $author->assignRole('team-member');
        $project->members()->attach($author->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'due_date' => today()->addWeek(),
        ]);

        Livewire::actingAs($author)
            ->test(TaskDetail::class, ['task' => $task])
            ->set('commentBody', 'Update progres 50%.')
            ->call('sendComment')
            ->assertHasNoErrors();

        $this->assertSame(1, $member->notifications()->count());
        $this->assertSame('comment', $member->notifications()->first()->data['kind']);
    }

    public function test_deadline_reminder_command_notifies_and_dedupes(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        Task::factory()->create([
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'todo',
            'due_date' => today()->addDay(),
        ]);

        $this->artisan('notifications:send-deadline-reminders')->assertSuccessful();
        $this->assertSame(1, $member->notifications()->count());

        // Jalan kedua di hari sama: tidak duplikat.
        $this->artisan('notifications:send-deadline-reminders')->assertSuccessful();
        $this->assertSame(1, $member->notifications()->count());
    }

    public function test_notifications_page_tabs_and_mark_read(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team-member');

        $task = Task::factory()->create(['assignee_id' => $member->id, 'created_by' => $member->id]);
        $member->notify(new TaskAssigned($task, 'PM'));

        $response = $this->actingAs($member)->get('/notifications');

        $response->assertOk();
        $response->assertSee('Notification Center');
        $response->assertSee('New assignment');

        $id = $member->notifications()->first()->id;

        Livewire::actingAs($member)
            ->test(NotificationCenter::class)
            ->set('tab', 'assigned')
            ->assertSee('New assignment')
            ->call('markRead', $id);

        $this->assertSame(0, $member->unreadNotifications()->count());
    }
}
