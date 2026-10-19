<?php

namespace Tests\Feature;

use App\Livewire\Reports;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_member_without_report_permission_is_forbidden(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team_member'); // tanpa view reports

        $this->actingAs($member)->get('/reports')->assertForbidden();
    }

    public function test_viewer_can_open_reports_with_project_data(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        $project = Project::factory()->create(['name' => 'Proyek Audit']);
        $project->members()->attach($viewer->id);

        Task::factory()->done()->create([
            'project_id' => $project->id,
            'assignee_id' => $viewer->id,
            'created_by' => $viewer->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($viewer)->get('/reports');

        $response->assertOk();
        $response->assertSee('Report & Performance Analytics');
        $response->assertSee('Proyek Audit');
    }

    public function test_manager_can_export_csv(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('project_manager');

        $project = Project::factory()->create();
        $project->members()->attach($manager->id);

        Task::factory()->create([
            'project_id' => $project->id,
            'assignee_id' => $manager->id,
            'created_by' => $manager->id,
            'title' => 'Task ekspor',
            'due_date' => today()->addWeek(),
        ]);

        $response = $this->actingAs($manager)->get(route('reports.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Task ekspor', $response->streamedContent());
    }

    public function test_member_cannot_export_csv(): void
    {
        $member = User::factory()->create();
        $member->assignRole('team_member'); // tanpa export reports

        $this->actingAs($member)->get(route('reports.export'))->assertForbidden();
    }
}
