<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ScreensTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('screenProvider')]
    public function test_stitch_screen_renders_inside_app_shell(string $uri, string $breadcrumb): void
    {
        $this->seed(RolePermissionSeeder::class);

        $member = User::factory()->create();
        $member->assignRole('team-member');

        $response = $this->actingAs($member)->get($uri);

        $response->assertOk();
        // Shell Stitch ter-render: sidebar + header.
        $response->assertSee('My Task');
        $response->assertSee($breadcrumb);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function screenProvider(): array
    {
        return [
            'dashboard' => ['/dashboard', 'Main Dashboard'],
            'tugas saya' => ['/tasks', 'My Tasks'],
            'buat tugas' => ['/tasks/create', 'Create Task'],
            'proyek' => ['/projects', 'Projects'],
            'board' => ['/board', 'Task Board'],
            'notifikasi' => ['/notifications', 'Notifications'],
            'pengaturan' => ['/settings', 'Settings'],
        ];
    }

    public function test_team_settings_screen_is_only_for_administrators(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/team')
            ->assertOk()
            ->assertSee('Team Settings');

        $member = User::factory()->create();
        $member->assignRole('team-member');

        $this->actingAs($member)->get('/team')->assertForbidden();
    }

    public function test_task_detail_mock_renders(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $member = User::factory()->create();
        $member->assignRole('team-member');
        $task = Task::factory()->create([
            'assignee_id' => $member->id,
            'created_by' => $member->id,
        ]);
        $task->project->members()->attach($member->id);

        $this->actingAs($member)->get("/tasks/{$task->id}")->assertOk();
    }

    public function test_guest_cannot_open_app_screens(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }
}
