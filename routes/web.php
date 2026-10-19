<?php

use App\Http\Controllers\Auth\SessionController;
use App\Livewire\Board;
use App\Livewire\CreateTask;
use App\Livewire\Dashboard;
use App\Livewire\MyTasks;
use App\Livewire\NotificationCenter;
use App\Livewire\Projects;
use App\Livewire\ProjectWorkspace;
use App\Livewire\Reports;
use App\Livewire\TaskDetail;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:6,1');
});

Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Stitch screens (wiring per screen to follow, currently static mocks):
    Route::get('/tasks', MyTasks::class)->name('tasks.mine');
    Route::get('/tasks/create', CreateTask::class)->name('tasks.create');
    Route::get('/tasks/{task}', TaskDetail::class)->name('tasks.show');
    Route::get('/tasks/{task}/attachments/{attachment}', function (Task $task, Attachment $attachment) {
        abort_unless($attachment->task_id === $task->id, 404);
        Gate::authorize('view', $task);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    })->name('attachments.download');
    Route::get('/projects', Projects::class)->name('projects.index');
    Route::get('/projects/{project}', ProjectWorkspace::class)->name('projects.show');
    Route::get('/board', Board::class)->name('board');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/reports/export', function (Request $request) {
        $projectId = (string) $request->query('project', 'all');

        $ids = Project::where('status', 'active')
            ->when(! auth()->user()->can('create projects'), fn ($q) => $q->whereHas('members', fn ($m) => $m->whereKey(auth()->id())))
            ->pluck('id')
            ->filter(fn (int $id) => Gate::allows('view', Project::find($id)));

        $tasks = Task::with(['project', 'assignee'])
            ->whereIn('project_id', $ids)
            ->when($projectId !== 'all', fn ($q) => $q->where('project_id', $projectId))
            ->orderBy('due_date')
            ->cursor();

        return response()->streamDownload(function () use ($tasks): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Project', 'Code', 'Title', 'Status', 'Priority', 'Assignee', 'Due Date', 'Completed At', 'Created At'], ';');

            foreach ($tasks as $task) {
                fputcsv($out, [
                    $task->project->name,
                    'TSK-'.$task->id,
                    $task->title,
                    $task->status,
                    $task->priority,
                    $task->assignee?->name ?? '',
                    $task->due_date->toDateString(),
                    $task->completed_at?->toDateTimeString() ?? '',
                    $task->created_at->toDateTimeString(),
                ], ';');
            }

            fclose($out);
        }, 'task-report-'.today()->toDateString().'.csv', ['Content-Type' => 'text/csv']);
    })->middleware('permission:export reports')->name('reports.export');
    Route::get('/notifications', NotificationCenter::class)->name('notifications');
    Route::view('/team', 'screens.pengaturan_tim')->name('team');
    Route::view('/settings', 'screens.pengaturan_tim')->name('settings');
});
