<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Observers\TaskObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Gate::define('manage-team', function (User $user): bool {
            return $user->can('user.manage') || $user->can('role.manage') || $user->can('user.invite');
        });

        Task::observe(TaskObserver::class);

        View::composer('layouts.app', function ($view): void {
            /** @var User $user */
            $user = auth()->user();

            $navItem = function (string $route, string $icon, string $label, string $match, ?int $badge = null, string $badgeTone = 'grey'): array {
                return [
                    'route' => $route,
                    'icon' => $icon,
                    'label' => $label,
                    'badge' => $badge,
                    'badge_tone' => $badgeTone,
                    'active' => request()->routeIs($match),
                ];
            };

            $myActive = Task::where('assignee_id', $user->id)->open()->count();
            $activeProjects = Project::where('status', 'active')
                ->when(! $user->can('project.create'), fn ($query) => $query->whereHas('members', fn ($member) => $member->whereKey($user->id)))
                ->count();
            $unread = $user->unreadNotifications()->count();
            $workspace = $user->projects()->where('status', 'active')->orderBy('name')->first();

            $tints = ['bg-secondary-fixed text-on-secondary-fixed', 'bg-tertiary-fixed text-on-tertiary-fixed', 'bg-surface-container-high text-primary', 'bg-surface-tint text-surface'];

            $view->with([
                'shellUser' => $user,
                'shellRole' => (string) str($user->getRoleNames()->first() ?? 'member')->replace('_', ' ')->title(),
                'userInitials' => $user->initials(),
                'userTint' => $tints[$user->id % 4],
                'workspaceName' => $workspace?->name ?? 'All Projects',
                'unreadCount' => $unread,
                'navMain' => array_values(array_filter([
                    $user->can('dashboard.view') ? $navItem('dashboard', 'layouts-grid-2x2', 'Dashboard', 'dashboard') : null,
                    $user->can('task.view') ? $navItem('tasks.mine', 'indicators-circle-check', 'My Tasks', 'tasks.*', $myActive ?: null) : null,
                    $user->can('project.view') ? $navItem('projects.index', 'documents-folder', 'Projects', 'projects.*', $activeProjects ?: null) : null,
                    $user->can('task.view') ? $navItem('board', 'layouts-kanban', 'Task Board', 'board*') : null,
                ])),
                'navSecondary' => array_values(array_filter([
                    $user->can('report.view') ? $navItem('reports', 'metrics-chart-bar', 'Reports & Analytics', 'reports*') : null,
                    $navItem('notifications', 'messaging-message-circle', 'Notifications', 'notifications*', $unread ?: null, 'red'),
                    $navItem('settings', 'tools-settings', 'Settings', 'settings*'),
                ])),
                'navAdmin' => array_values(array_filter([
                    $user->can('manage-team') ? $navItem('team', 'identity-users', 'Team Members', 'team*') : null,
                ])),
            ]);
        });
    }
}
