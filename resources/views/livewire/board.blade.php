<div class="px-space-xl py-space-md flex flex-col gap-space-md max-w-[1600px] w-full mx-auto" x-data="{ dragId: null }">
    {{-- Header --}}
    <div class="flex flex-col gap-space-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-sm">
            <div class="flex items-center gap-space-sm min-w-0">
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight truncate">
                    Task Board{{ $activeProject ? ' — '.$activeProject->name : '' }}
                </h1>
                <div class="relative shrink-0">
                    <select wire:model.live="project_id" class="h-8 pl-2.5 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none shadow-sm max-w-64">
                        <option value="all">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                </div>
            </div>
            <div class="flex items-center gap-space-xs shrink-0">
                <div class="flex items-center p-1 bg-surface-container rounded-xl shadow-sm">
                    <a href="{{ route('tasks.mine') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-default text-label-default transition-all">
                        <x-rune-icon name="tools-list-filter" class="w-[18px] h-[18px]" />
                        <span>List / Tabel</span>
                    </a>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-lowest text-secondary shadow-sm font-label-default text-label-default">
                        <x-rune-icon name="layouts-kanban" class="w-[18px] h-[18px]" />
                        <span>Kanban</span>
                    </span>
                </div>
                @if ($canCreate)
                    <a href="{{ route('tasks.create', $activeProject ? ['project' => $activeProject->id] : []) }}" class="flex items-center gap-1.5 h-8 px-3 rounded-lg bg-secondary text-on-secondary hover:bg-secondary-container font-body-medium text-body-medium shadow-sm transition-colors">
                        <x-rune-icon name="indicators-plus" class="w-4 h-4" />
                        <span>Add Task</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="flex flex-wrap items-center gap-space-sm">
            <div class="relative w-full sm:w-56">
                <x-rune-icon name="tools-search" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                <input wire:model.live.debounce.300ms="q" type="text" placeholder="Search tasks on this board..."
                    class="w-full h-8 pl-8 pr-space-sm bg-surface-container-lowest text-on-surface placeholder:text-outline rounded-lg font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm transition-all" />
            </div>
            <div class="relative">
                <select wire:model.live="assignee_id" class="h-8 pl-2.5 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none shadow-sm max-w-48">
                    <option value="all">All Members</option>
                    @foreach ($assignees as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            </div>
            <div class="relative">
                <select wire:model.live="priority" class="h-8 pl-2.5 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none shadow-sm">
                    <option value="all">All Priorities</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <x-rune-icon name="tools-flag" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            </div>
            <div class="relative">
                <select wire:model.live="tag" class="h-8 pl-2.5 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none shadow-sm max-w-44">
                    <option value="all">All Labels</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag }}">#{{ $tag }}</option>
                    @endforeach
                </select>
                <x-rune-icon name="money-tag" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            </div>
            <button wire:click="$toggle('overdueOnly')" type="button"
                class="flex items-center gap-1.5 h-8 px-3 rounded-lg font-body-medium text-body-medium transition-colors {{ $overdueOnly ? 'bg-error-container/50 text-error' : 'bg-surface-container-lowest text-on-surface-variant hover:text-on-surface shadow-sm' }}">
                <x-rune-icon name="indicators-triangle-alert" class="w-4 h-4" />
                <span>Overdue Only</span>
                <span class="px-1.5 py-0.5 rounded-full font-label-default text-label-default leading-none {{ $overdueOnly ? 'bg-error text-on-error' : 'bg-surface-container text-on-surface-variant' }}">{{ $overdueCount }}</span>
            </button>
        </div>
    </div>

    {{-- Columns --}}
    <div class="px-space-xl pb-space-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-lg items-start">
            @foreach ($columns as $column)
                @php
                    $dot = match ($column['status']) {
                        'todo' => 'bg-outline',
                        'in_progress' => 'bg-secondary',
                        'review' => 'bg-primary-container',
                        default => 'bg-on-tertiary-container',
                    };
                @endphp
                <div class="flex flex-col bg-surface-container-low rounded-xl p-space-sm shadow-sm min-h-[480px]"
                    data-column="{{ $column['status'] }}"
                    x-on:dragover.prevent="$el.classList.add('ring-2', 'ring-secondary/40')"
                    x-on:dragleave="$el.classList.remove('ring-2', 'ring-secondary/40')"
                    x-on:drop.prevent="$el.classList.remove('ring-2', 'ring-secondary/40'); if (dragId) $wire.moveTask(dragId, '{{ $column['status'] }}')">
                    <div class="flex items-center justify-between px-space-xs py-space-xs mb-space-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
                            <span class="font-title-sm text-title-sm text-on-surface">{{ $column['label'] }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container font-label-default text-label-default text-on-surface-variant font-semibold">{{ $column['tasks']->count() }}</span>
                        </div>
                        @if ($canCreate)
                            <a href="{{ route('tasks.create', $activeProject ? ['project' => $activeProject->id] : []) }}" title="Add task" class="p-1 rounded text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors">
                                <x-rune-icon name="indicators-plus" class="w-[18px] h-[18px]" />
                            </a>
                        @endif
                    </div>
                    <div class="flex flex-col gap-space-sm">
                        @forelse ($column['tasks'] as $task)
                            @php
                                $isOverdue = $task->status !== 'done' && $task->due_date->lt(today());
                                $prioStyle = match ($task->priority) {
                                    'critical' => 'bg-error text-on-error',
                                    'high' => 'bg-secondary-fixed text-on-secondary-fixed',
                                    'medium' => 'bg-surface-container text-on-surface-variant',
                                    default => 'bg-surface-container-low text-outline',
                                };
                                $prioLabel = ['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'][$task->priority] ?? $task->priority;
                            @endphp
                            <div class="group bg-surface-container-lowest p-space-md rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-all relative {{ $task->rowCanUpdate ? 'cursor-grab active:cursor-grabbing' : '' }}"
                                @if ($task->rowCanUpdate) draggable="true" x-on:dragstart="dragId = {{ $task->id }}" x-on:dragend="dragId = null" @endif>
                                <div class="flex items-center justify-between mb-space-xs">
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-uppercase text-label-uppercase truncate max-w-[60%]">{{ $task->project->name }}</span>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="px-1.5 py-0.5 rounded font-code-sm text-code-sm font-semibold {{ $prioStyle }}">{{ $prioLabel }}</span>
                                        @if ($task->rowCanUpdate)
                                            <x-rune-icon name="layouts-grip-horizontal" class="text-outline w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        @endif
                                    </div>
                                </div>
                                <div class="font-code-sm text-code-sm text-secondary font-semibold mb-1">TSK-{{ $task->id }}</div>
                                <h3 class="font-body-medium text-body-medium text-on-surface font-semibold mb-space-sm leading-snug">
                                    <a href="{{ route('tasks.show', $task) }}" class="hover:text-secondary transition-colors">{{ $task->title }}</a>
                                </h3>
                                <div class="flex items-center gap-space-xs font-body-sm text-body-sm mb-space-md {{ $isOverdue ? 'text-error font-medium' : 'text-on-surface-variant' }}">
                                    <x-rune-icon name="schedule-calendar" class="w-[15px] h-[15px] {{ $isOverdue ? '' : 'text-outline' }}" />
                                    <span>{{ $isOverdue ? 'Overdue '.$task->due_date->diffInDays(today()).' days' : $task->due_date->locale('en')->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-space-xs">
                                    <div class="flex items-center gap-space-sm text-outline font-body-sm text-body-sm">
                                        @if (($task->subtasks_count ?? 0) > 0)
                                            <span class="flex items-center gap-1" title="{{ $task->done_subtasks_count }}/{{ $task->subtasks_count }} subtasks completed">
                                                <x-rune-icon name="documents-clipboard-list" class="w-[15px] h-[15px]" />
                                                <span class="text-on-surface-variant font-code-sm">{{ $task->done_subtasks_count }}/{{ $task->subtasks_count }}</span>
                                            </span>
                                        @endif
                                        @if (($task->comments_count ?? 0) > 0)
                                            <span class="flex items-center gap-1" title="{{ $task->comments_count }} Comments">
                                                <x-rune-icon name="messaging-message-circle" class="w-[15px] h-[15px]" />
                                                <span class="text-on-surface-variant font-code-sm">{{ $task->comments_count }}</span>
                                            </span>
                                        @endif
                                        @if (($task->attachments_count ?? 0) > 0)
                                            <span class="flex items-center gap-1" title="{{ $task->attachments_count }} Attachments">
                                                <x-rune-icon name="documents-paperclip" class="w-[15px] h-[15px]" />
                                                <span class="text-on-surface-variant font-code-sm">{{ $task->attachments_count }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    @if ($task->assignee)
                                        <div class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center text-[10px] font-bold" title="{{ $task->assignee->name }}">{{ $task->assignee->initials() }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="border-2 border-dashed border-outline-variant/50 rounded-xl p-space-md text-center font-body-sm text-body-sm text-outline">
                                Drag tasks here
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
