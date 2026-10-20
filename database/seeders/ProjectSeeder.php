<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'pm@example.com')->first()
            ?? User::role('project-manager')->first()
            ?? User::first();

        $member = User::where('email', 'member@example.com')->first()
            ?? User::role('team-member')->first()
            ?? User::first();

        $viewer = User::where('email', 'viewer@example.com')->first()
            ?? User::role('viewer')->first()
            ?? User::first();

        foreach (['Website Redesign', 'Mobile App Launch'] as $index => $name) {
            $project = Project::factory()->create([
                'name' => $name,
                'created_by' => $manager->id,
            ]);

            $project->members()->syncWithoutDetaching([$manager->id, $member->id, $viewer->id]);

            $milestones = Milestone::factory(2)->create([
                'project_id' => $project->id,
            ]);

            $tasks = Task::factory(4)->create([
                'project_id' => $project->id,
                'milestone_id' => $milestones[$index % 2]->id,
                'assignee_id' => $member->id,
                'created_by' => $manager->id,
            ]);

            Task::factory()->inProgress()->create([
                'project_id' => $project->id,
                'milestone_id' => $milestones[0]->id,
                'assignee_id' => $member->id,
                'created_by' => $manager->id,
            ]);

            Task::factory()->done()->create([
                'project_id' => $project->id,
                'milestone_id' => $milestones[1]->id,
                'assignee_id' => $member->id,
                'created_by' => $manager->id,
            ]);

            Task::factory(2)->create([
                'project_id' => $project->id,
                'parent_id' => $tasks[0]->id,
                'milestone_id' => null,
                'assignee_id' => $member->id,
                'created_by' => $member->id,
            ]);

            foreach ($tasks as $task) {
                Comment::factory(2)->create([
                    'task_id' => $task->id,
                    'user_id' => $member->id,
                ]);

                Attachment::factory()->create([
                    'task_id' => $task->id,
                    'user_id' => $member->id,
                ]);
            }

            Comment::factory()->create([
                'task_id' => $tasks[0]->id,
                'user_id' => $viewer->id,
            ]);
        }
    }
}
