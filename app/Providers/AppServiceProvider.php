<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
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
            $activeProjects = Project::where('status', 'active')->count();
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
                'navMain' => [
                    $navItem('dashboard', 'layouts-grid-2x2', 'Dashboard', 'dashboard'),
                    $navItem('tasks.mine', 'indicators-circle-check', 'My Tasks', 'tasks.*', $myActive ?: null),
                    $navItem('projects.index', 'documents-folder', 'Projects', 'projects.*', $activeProjects ?: null),
                    $navItem('board', 'layouts-kanban', 'Task Board', 'board*'),
                ],
                'navSecondary' => [
                    $navItem('reports', 'metrics-chart-bar', 'Reports & Analytics', 'reports*'),
                    $navItem('notifications', 'messaging-message-circle', 'Notifications', 'notifications*', $unread ?: null, 'red'),
                ],
                'navAdmin' => [
                    $navItem('team', 'identity-users', 'Team Members', 'team*'),
                    $navItem('settings', 'tools-settings', 'Settings', 'settings*'),
                ],
            ]);
        });
    }
}
