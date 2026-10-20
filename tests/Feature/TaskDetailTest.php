<?php

namespace Tests\Feature;

use App\Livewire\TaskDetail;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TaskDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function setupTask(): array
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $member->assignRole('team-member');
        $project->members()->attach($member->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'title' => 'Audit keamanan siber',
        ]);

        return [$project, $member, $task];
    }

    public function test_member_can_view_own_project_task(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $this->actingAs($member)->get("/tasks/{$task->id}")
            ->assertOk()
            ->assertSee('Audit keamanan siber');
    }

    public function test_non_member_cannot_view_task(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $outsider = User::factory()->create();
        $outsider->assignRole('team-member');

        $this->actingAs($outsider)->get("/tasks/{$task->id}")->assertForbidden();
    }

    public function test_member_can_comment_and_manager_can_delete_it(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $commentId = Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->set('commentBody', 'Sudah dicek, tinggal deploy.')
            ->call('sendComment')
            ->assertHasNoErrors()
            ->viewData('comments')->first()->id;

        $this->assertSame('Sudah dicek, tinggal deploy.', Comment::find($commentId)->body);

        $manager = User::factory()->create();
        $manager->assignRole('project-manager');
        $project->members()->attach($manager->id);

        Livewire::actingAs($manager)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteComment', $commentId);

        $this->assertNull(Comment::find($commentId));
    }

    public function test_member_cannot_delete_others_comment(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $comment = Comment::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $other = User::factory()->create();
        $other->assignRole('team-member');
        $project->members()->attach($other->id);

        Livewire::actingAs($other)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteComment', $comment->id)
            ->assertForbidden();
    }

    public function test_member_can_upload_and_delete_own_attachment(): void
    {
        Storage::fake('public');
        [$project, $member, $task] = $this->setupTask();

        $file = UploadedFile::fake()->create('desain.png', 200, 'image/png');

        Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->set('uploads', [$file]);

        $attachment = Attachment::where('task_id', $task->id)->firstOrFail();
        Storage::disk('public')->assertExists($attachment->file_path);

        // Unduh butuh akses view proyek.
        $this->actingAs($member)
            ->get(route('attachments.download', [$task, $attachment]))
            ->assertOk();

        Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteAttachment', $attachment->id);

        $this->assertNull(Attachment::find($attachment->id));
    }

    public function test_member_cannot_delete_others_attachment_but_manager_can(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $attachment = Attachment::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $other = User::factory()->create();
        $other->assignRole('team-member');
        $project->members()->attach($other->id);

        Livewire::actingAs($other)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteAttachment', $attachment->id)
            ->assertForbidden();

        $manager = User::factory()->create();
        $manager->assignRole('project-manager');
        $project->members()->attach($manager->id);

        Livewire::actingAs($manager)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('deleteAttachment', $attachment->id);

        $this->assertNull(Attachment::find($attachment->id));
    }

    public function test_member_can_add_and_complete_subtask(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $component = Livewire::actingAs($member)
            ->test(TaskDetail::class, ['task' => $task])
            ->set('subtaskTitle', 'Siapkan data uji')
            ->call('addSubtask')
            ->assertHasNoErrors();

        $subtask = Task::where('parent_id', $task->id)->firstOrFail();
        $this->assertSame('Siapkan data uji', $subtask->title);

        $component->call('toggleSubtask', $subtask->id);

        $this->assertSame('done', $subtask->fresh()->status);
    }

    public function test_viewer_sees_readonly_detail(): void
    {
        [$project, $member, $task] = $this->setupTask();

        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');
        $project->members()->attach($viewer->id);

        $response = $this->actingAs($viewer)->get("/tasks/{$task->id}");

        $response->assertOk();
        // Viewer boleh komentar (matriks PRD) tapi tidak bisa edit/status.
        $response->assertSee('Read only');
        $response->assertSee('Send Comment');
        $response->assertDontSee('Edit');
    }
}
