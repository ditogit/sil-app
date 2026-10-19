@php
    $isOverdue = $task->status !== 'done' && $task->due_date->lt(today());
    $priorityPill = match ($task->priority) {
        'critical' => ['bg-error text-on-error', 'other-zap', 'Critical', true],
        'high' => ['bg-secondary-fixed text-on-secondary-fixed-variant', 'arrows-arrow-up', 'High', false],
        'medium' => ['bg-surface-container text-on-surface-variant', null, 'Medium', false],
        default => ['bg-surface-container-low text-outline', null, 'Low', false],
    };
    $statusPill = match ($task->status) {
        'in_progress' => ['bg-surface-container-high text-secondary', 'In Progress', true],
        'review' => ['bg-surface-container-high text-primary', 'Review', false],
        'done' => ['bg-tertiary-fixed/40 text-on-tertiary-fixed-variant', 'Done', false],
        default => ['bg-surface-container text-on-surface-variant', 'To Do', false],
    };
    $subTotal = $task->subtasks_count ?? 0;
    $subDone = $task->done_subtasks_count ?? 0;
@endphp
<div class="group relative flex flex-col md:flex-row md:items-center justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all gap-space-md">
    <div class="flex items-start md:items-center gap-space-md min-w-0 flex-1">
        <div class="pt-1 md:pt-0 shrink-0 flex items-center">
            <input wire:click="toggleDone({{ $task->id }})" type="checkbox" @checked($task->status === 'done') @disabled(! $task->rowCanUpdate)
                class="w-4 h-4 rounded accent-secondary cursor-pointer disabled:cursor-not-allowed disabled:opacity-50" />
        </div>
        <div class="flex flex-col gap-1 min-w-0">
            <div class="flex flex-wrap items-center gap-space-xs">
                <span class="font-code-sm text-code-sm text-secondary font-bold">TSK-{{ $task->id }}</span>
                <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                <span class="px-2 py-0.5 rounded-md bg-surface-container font-label-default text-label-default text-on-surface-variant font-medium">{{ $task->project->name }}</span>
                <span class="px-2 py-0.5 rounded-md font-label-default text-label-default font-semibold flex items-center gap-1 {{ $priorityPill[0] }}">
                    @if ($priorityPill[1])<x-rune-icon name="{{ $priorityPill[1] }}" class="w-[13px] h-[13px]" />@endif
                    {{ $priorityPill[2] }}
                </span>
                @if ($isOverdue)
                    <span class="px-2 py-0.5 rounded-md bg-error/10 text-error font-label-default text-label-default font-bold flex items-center gap-1">
                        <x-rune-icon name="schedule-alarm-clock" class="w-[13px] h-[13px]" />
                        Overdue {{ $task->due_date->diffInDays(today()) }} days ({{ $task->due_date->locale('en')->translatedFormat('d M Y') }})
                    </span>
                @elseif ($task->status !== 'done')
                    <span class="font-label-default text-label-default text-outline">
                        Due {{ $task->due_date->locale('en')->translatedFormat('d M Y') }}{{ $task->due_time ? ' • '.$task->due_time->format('H:i') : '' }}
                    </span>
                @endif
            </div>
            <a href="{{ route('tasks.show', $task) }}" class="font-title-sm text-title-sm text-on-surface hover:text-secondary font-semibold transition-colors truncate {{ $task->status === 'done' ? 'line-through opacity-70' : '' }}">
                {{ $task->title }}
            </a>
            <div class="flex items-center gap-space-md text-on-surface-variant font-body-sm text-body-sm">
                @if ($subTotal > 0)
                    <span class="flex items-center gap-1">
                        <x-rune-icon name="documents-clipboard-list" class="w-[15px] h-[15px] text-secondary" /> {{ $subDone }}/{{ $subTotal }} Sub-task
                    </span>
                @endif
                @if (($task->attachments_count ?? 0) > 0)
                    <span class="flex items-center gap-1">
                        <x-rune-icon name="documents-paperclip" class="w-[15px] h-[15px]" /> {{ $task->attachments_count }}
                    </span>
                @endif
                @if (($task->comments_count ?? 0) > 0)
                    <span class="flex items-center gap-1">
                        <x-rune-icon name="messaging-message-circle" class="w-[15px] h-[15px]" /> {{ $task->comments_count }} Comments
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between md:justify-end gap-space-md shrink-0 border-t md:border-t-0 pt-2 md:pt-0">
        @if ($subTotal > 0)
            <div class="hidden lg:flex flex-col gap-1 w-28">
                <div class="flex justify-between font-label-default text-label-default text-on-surface-variant">
                    <span>Progress</span>
                    <span class="font-semibold text-secondary">{{ (int) round($subDone / $subTotal * 100) }}%</span>
                </div>
                <div class="h-1.5 w-full bg-surface-container rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: {{ (int) round($subDone / $subTotal * 100) }}%"></div>
                </div>
            </div>
        @endif
        @if ($task->rowCanUpdate)
            <div x-data="{ open: false }" class="relative">
                <button x-on:click="open = ! open" type="button"
                    class="flex items-center gap-1.5 h-8 px-2.5 rounded-lg font-label-default text-label-default font-semibold hover:bg-surface-container transition-colors {{ $statusPill[0] }}">
                    @if ($statusPill[2])<span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>@endif
                    <span>{{ $statusPill[1] }}</span>
                    <x-rune-icon name="arrows-chevron-down" class="w-4 h-4" />
                </button>
                <div x-show="open" x-cloak x-on:click.outside="open = false"
                    class="absolute right-0 mt-1 w-40 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/40 overflow-hidden z-30">
                    @foreach (['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label)
                        <button wire:click="setStatus({{ $task->id }}, '{{ $value }}')" x-on:click="open = false" type="button"
                            class="w-full text-left px-space-md py-2 font-body-sm text-body-sm hover:bg-surface-container-low transition-colors {{ $task->status === $value ? 'text-secondary font-semibold' : 'text-on-surface-variant' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button wire:click="toggleDone({{ $task->id }})" title="{{ $task->status === 'done' ? 'Reopen' : 'Mark as Done' }}" type="button"
                    class="p-1.5 text-on-surface-variant hover:text-on-tertiary-fixed-variant hover:bg-surface-container rounded-lg transition-colors">
                    <x-rune-icon name="indicators-circle-check" class="w-[18px] h-[18px]" />
                </button>
                <div x-data="{ open: false }" class="relative">
                    <button x-on:click="open = ! open" title="Menu" type="button"
                        class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors">
                        <x-rune-icon name="layouts-grip-vertical" class="w-[18px] h-[18px]" />
                    </button>
                    <div x-show="open" x-cloak x-on:click.outside="open = false"
                        class="absolute right-0 mt-1 w-40 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/40 overflow-hidden z-30">
                        <a href="{{ route('tasks.show', $task) }}"
                            class="block px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface">Task Details</a>
                        @if ($canDelete ?? false)
                            <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Delete this task? It will be moved to the archive (soft delete)."
                                type="button" class="w-full text-left px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container/30">Delete</button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <span class="px-2.5 py-0.5 rounded-full font-label-default text-label-default font-semibold {{ $statusPill[0] }}">{{ $statusPill[1] }}</span>
        @endif
    </div>
</div>
