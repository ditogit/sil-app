<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Notifications', 'breadcrumb' => 'Notifications'])]
class NotificationCenter extends Component
{
    use WithPagination;

    public string $tab = 'all';

    public string $q = '';

    public int $perPage = 15;

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->whereKey($id)->first()?->markAsRead();
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function dismiss(string $id): void
    {
        auth()->user()->notifications()->whereKey($id)->delete();
    }

    public function archiveOld(): void
    {
        auth()->user()->notifications()
            ->whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        session()->flash('success', 'Read notifications older than 30 days were archived.');
    }

    public static function kindMeta(string $kind): array
    {
        return match ($kind) {
            'assigned' => ['identity-user-plus', 'bg-secondary-fixed text-on-secondary-fixed-variant', 'Assignment'],
            'comment' => ['messaging-message-circle', 'bg-surface-container-high text-primary', 'Comment'],
            'status' => ['schedule-refresh-cw', 'bg-surface-container text-on-surface-variant', 'Status'],
            default => ['schedule-alarm-clock', 'bg-error-container/60 text-error', 'Deadline'],
        };
    }

    public function render(): View
    {
        $user = auth()->user();
        $base = $user->notifications()->latest();

        $counts = [
            'all' => (clone $base)->count(),
            'unread' => $user->unreadNotifications()->count(),
            'deadline' => (clone $base)->where('data->kind', 'deadline')->count(),
            'assigned' => (clone $base)->where('data->kind', 'assigned')->count(),
            'status' => (clone $base)->where('data->kind', 'status')->count(),
            'comment' => (clone $base)->where('data->kind', 'comment')->count(),
        ];

        $query = match ($this->tab) {
            'unread' => $user->unreadNotifications()->latest(),
            'deadline' => (clone $base)->where('data->kind', 'deadline'),
            'assigned' => (clone $base)->where('data->kind', 'assigned'),
            'status' => (clone $base)->where('data->kind', 'status'),
            'comment' => (clone $base)->where('data->kind', 'comment'),
            default => $base,
        };

        if ($this->q !== '') {
            $term = '%'.mb_strtolower($this->q).'%';
            $query->where(fn ($q) => $q->whereRaw('lower(data->>\'title\') like ?', [$term])
                ->orWhereRaw('lower(data->>\'message\') like ?', [$term]));
        }

        return view('livewire.notification-center', [
            'counts' => $counts,
            'items' => $query->paginate($this->perPage),
        ]);
    }
}
