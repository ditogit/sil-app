<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1600px] w-full mx-auto">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-space-xxs min-w-0">
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Notification Center</h1>
            <p class="font-body-default text-body-default text-on-surface-variant">Assignments, comments, status changes, and deadline reminders for you.</p>
        </div>
        <div class="flex items-center gap-space-sm shrink-0">
            <button wire:click="markAllRead" type="button" class="flex items-center gap-1.5 h-9 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors">
                <x-rune-icon name="indicators-square-check" class="w-[18px] h-[18px]" />
                <span>Mark All as Read</span>
            </button>
            <button wire:click="archiveOld" wire:confirm="Archive (delete) notifications read more than 30 days ago?" type="button" class="h-9 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors">
                Archive &gt; 30 Days
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
        <div class="flex items-center gap-space-xs overflow-x-auto pb-1">
            @foreach (['all' => 'All', 'unread' => 'Unread', 'deadline' => 'Due & SLA', 'assigned' => 'New Assignments', 'status' => 'Status Updates', 'comment' => 'Comments'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')" type="button"
                    class="px-space-md py-1.5 rounded-lg font-body-medium text-body-medium transition-all flex items-center gap-1.5 whitespace-nowrap {{ $tab === $key ? 'bg-surface-container text-secondary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    @if ($key === 'unread' && $counts['unread'] > 0)<span class="w-1.5 h-1.5 rounded-full bg-error"></span>@endif
                    {{ $label }}
                    <span class="px-1.5 py-0.5 rounded-full font-label-default text-label-default leading-none {{ $tab === $key ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant' }}">{{ $counts[$key] }}</span>
                </button>
            @endforeach
        </div>
        <div class="relative w-full sm:w-72">
            <x-rune-icon name="tools-search" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            <input wire:model.live.debounce.300ms="q" type="text" placeholder="Search notifications..."
                class="w-full h-8 pl-8 pr-space-sm bg-surface-container-low text-on-surface placeholder:text-outline rounded-lg font-body-sm text-body-sm outline-none focus:bg-surface-container transition-all" />
        </div>
    </div>

    <div class="flex flex-col gap-space-sm">
        @forelse ($items as $item)
            @php
                [$icon, $tint, $kindLabel] = \App\Livewire\NotificationCenter::kindMeta($item->data['kind'] ?? 'deadline');
                $taskId = $item->data['task_id'] ?? null;
            @endphp
            <div class="flex items-start gap-space-md p-space-md rounded-xl shadow-sm transition-all {{ $item->read_at ? 'bg-surface-container-lowest' : 'bg-surface-container-lowest ring-1 ring-secondary/40' }}">
                <div class="w-10 h-10 rounded-xl {{ $tint }} flex items-center justify-center shrink-0">
                    <x-rune-icon name="{{ $icon }}" class="w-5 h-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">{{ $kindLabel }}</span>
                        @if (! $item->read_at)<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>@endif
                        <span class="font-label-default text-label-default text-outline">{{ $item->created_at->locale('en')->diffForHumans() }}</span>
                    </div>
                    <p class="font-title-sm text-title-sm text-on-surface font-semibold mt-0.5">{{ $item->data['title'] ?? 'Notification' }}</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $item->data['message'] ?? '' }}</p>
                    @if ($taskId)
                        <a href="{{ route('tasks.show', $taskId) }}" class="inline-flex items-center gap-1 mt-1.5 text-secondary font-body-sm text-body-sm font-semibold hover:underline">
                            Open Task Details <x-rune-icon name="arrows-chevron-right" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    @if (! $item->read_at)
                        <button wire:click="markRead('{{ $item->id }}')" type="button" title="Mark as read" class="p-1.5 text-on-surface-variant hover:text-secondary rounded-lg hover:bg-surface-container transition-colors">
                            <x-rune-icon name="indicators-circle-check" class="w-[18px] h-[18px]" />
                        </button>
                    @endif
                    <button wire:click="dismiss('{{ $item->id }}')" type="button" title="Delete" class="p-1.5 text-on-surface-variant hover:text-error rounded-lg hover:bg-error-container transition-colors">
                        <x-rune-icon name="tools-trash" class="w-[18px] h-[18px]" />
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-xl text-center">
                <p class="font-body-medium text-body-medium text-on-surface font-semibold">No notifications</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">You're all caught up. New assignments and reminders will show up here.</p>
            </div>
        @endforelse
    </div>

    @if ($items->hasMorePages())
        <div class="flex justify-center">
            <button wire:click="$set('perPage', {{ $perPage + 15 }})" type="button" class="h-9 px-space-lg bg-surface-container-lowest rounded-xl font-body-medium text-body-medium text-secondary hover:bg-surface-container shadow-sm transition-colors">
                Load Previous Notifications
            </button>
        </div>
    @endif
</div>
