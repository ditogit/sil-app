<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1600px] w-full mx-auto">
    {{-- Page header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-space-xxs min-w-0">
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">My Tasks</h1>
            <p class="font-body-default text-body-default text-on-surface-variant">Manage, filter by time and priority, and track the progress of your responsibilities across all projects.</p>
        </div>
        <div class="flex items-center gap-space-sm self-start lg:self-auto shrink-0">
            <div class="flex items-center p-1 bg-surface-container rounded-xl shadow-sm">
                <button type="button" aria-pressed="true" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-lowest text-secondary shadow-sm font-label-default text-label-default transition-all">
                    <x-rune-icon name="tools-list-filter" class="w-[18px] h-[18px]" />
                    <span>List View</span>
                </button>
                <a href="{{ route('board') }}" aria-pressed="false" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-default text-label-default transition-all">
                    <x-rune-icon name="layouts-kanban" class="w-[18px] h-[18px]" />
                    <span>Kanban View</span>
                </a>
            </div>
            @if ($canCreate)
                <button wire:click="openQuickCreate" type="button" class="flex items-center gap-1.5 h-9 px-space-md bg-secondary text-on-secondary hover:bg-secondary-container transition-colors rounded-xl font-body-medium text-body-medium shadow-sm cursor-pointer">
                    <x-rune-icon name="documents-file-plus" class="w-[18px] h-[18px]" />
                    <span>Add Personal Task</span>
                </button>
                <livewire:quick-create-task />
            @endif
        </div>
    </div>

    {{-- KPI --}}
    @php
        $total = max(1, $counts['all'] + $counts['done']);
        $kpi = [
            ['Total Assigned', 'documents-clipboard-list', 'text-secondary', $counts['all'] + $counts['done'], '', $counts['all'] + $counts['done'], $total, 'bg-secondary', false],
            ['Today', 'schedule-calendar', 'text-secondary', $counts['today'], '', $counts['today'], $total, 'bg-secondary', false],
            ['Upcoming', 'schedule-clock', 'text-secondary', $counts['upcoming'], '', $counts['upcoming'], $total, 'bg-outline', false],
            ['Overdue', 'indicators-triangle-alert', 'text-error', $counts['overdue'], 'Act Now!', $counts['overdue'], $total, 'bg-error', true],
            ['Completed This Week', 'indicators-square-check', 'text-tertiary-fixed-dim', $counts['doneWeek'], '', $counts['doneWeek'], $total, 'bg-tertiary-fixed-dim', false],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-space-md">
        @foreach ($kpi as [$label, $icon, $iconColor, $value, $sub, $bar, $max, $barColor, $alert])
            <div class="p-space-md {{ ($alert ?? false) ? 'bg-error-container/40' : 'bg-surface-container-lowest' }} rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="font-label-uppercase text-label-uppercase {{ ($alert ?? false) ? 'text-error font-bold' : 'text-on-surface-variant' }} uppercase {{ ($alert ?? false) ? 'tracking-wider' : '' }}">{{ $label }}</span>
                    <x-rune-icon name="{{ $icon }}" class="{{ $iconColor }} w-5 h-5" />
                </div>
                <div class="mt-space-md flex items-baseline gap-2">
                    <span class="font-headline-xl text-headline-xl {{ ($alert ?? false) ? 'text-error' : 'text-on-surface' }} font-bold">{{ $value }}</span>
                    @if ($sub)<span class="font-body-sm text-body-sm {{ ($alert ?? false) ? 'text-error font-medium' : 'text-on-tertiary-fixed-variant' }}">{{ $sub }}</span>@endif
                </div>
                <div class="mt-2 w-full {{ ($alert ?? false) ? 'bg-error-container' : 'bg-surface-container' }} h-1 rounded-full overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full" style="width: {{ (int) round($bar / $max * 100) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="flex flex-col gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
        <div class="flex items-center justify-between pb-1 overflow-x-auto">
            <div class="flex items-center gap-space-xs shrink-0" role="tablist">
                @foreach (['all' => 'All', 'today' => 'Today', 'upcoming' => 'Upcoming', 'overdue' => 'Overdue', 'done' => 'Completed'] as $key => $label)
                    @php $isOverdueTab = $key === 'overdue'; $isActive = $tab === $key; @endphp
                    <button wire:click="setTab('{{ $key }}')" role="tab" aria-selected="{{ $isActive ? 'true' : 'false' }}" type="button"
                        class="px-space-md py-1.5 rounded-lg font-body-medium text-body-medium transition-all flex items-center gap-1.5 {{ $isActive ? ($isOverdueTab ? 'bg-error-container/40 text-error' : 'bg-surface-container text-secondary') : ($isOverdueTab ? 'text-error hover:bg-error-container/40' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low') }}">
                        <span class="flex items-center gap-1">
                            @if ($isOverdueTab && ! $isActive)<span class="w-1.5 h-1.5 rounded-full bg-error"></span>@endif
                            {{ $label }}
                        </span>
                        <span class="px-1.5 py-0.5 rounded-full font-label-default text-label-default leading-none {{ $isActive ? ($isOverdueTab ? 'bg-error text-on-error' : 'bg-secondary text-on-secondary') : 'bg-surface-container text-on-surface-variant' }}">{{ $counts[$key] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="flex items-center gap-2 pl-space-md shrink-0">
                <label class="flex items-center gap-2 cursor-pointer select-none py-1 px-2 rounded-lg hover:bg-surface-container-low transition-colors">
                    <input wire:model.live="showSubtasks" type="checkbox" class="w-4 h-4 rounded accent-secondary cursor-pointer" />
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Show Sub-tasks</span>
                </label>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-space-sm pt-2">
            <div class="flex flex-wrap items-center gap-space-sm flex-1">
                <div class="relative w-full sm:w-64">
                    <x-rune-icon name="tools-list-filter" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                    <input wire:model.live.debounce.300ms="q" type="text" placeholder="Search my tasks..."
                        class="w-full h-8 pl-8 pr-space-sm bg-surface-container-low text-on-surface placeholder:text-outline rounded-lg font-body-sm text-body-sm outline-none focus:bg-surface-container transition-all" />
                </div>
                <div class="relative">
                    <select wire:model.live="project_id" class="h-8 pl-2.5 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none hover:bg-surface-container transition-colors">
                        <option value="all">All Projects</option>
                        @foreach ($myProjects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                </div>
                <div class="relative">
                    <select wire:model.live="priority" class="h-8 pl-2.5 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none hover:bg-surface-container transition-colors">
                        <option value="all">All Priorities</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Sort:</span>
                <div class="relative">
                    <select wire:model.live="sort" class="h-8 pl-2 pr-7 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-lg appearance-none cursor-pointer outline-none hover:bg-surface-container transition-colors font-medium">
                        <option value="due">Nearest Due Date</option>
                        <option value="priority">Highest Priority</option>
                        <option value="updated">Last Activity</option>
                        <option value="title">Task Name (A-Z)</option>
                    </select>
                    <x-rune-icon name="arrows-unfold-vertical" class="absolute right-1.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                </div>
            </div>
        </div>
    </div>

    {{-- Groups --}}
    <div class="flex flex-col gap-space-lg">
        @php $visibleCount = collect($groups)->sum(fn ($g) => $g['tasks']->count()); @endphp
        @if ($visibleCount > 0)
            @foreach ($groups as $group)
                @if ($group['tasks']->isNotEmpty())
                <div class="flex flex-col gap-space-xs">
                    <div class="flex items-center justify-between px-space-xs py-1">
                        <div class="flex items-center gap-space-sm">
                            @if ($group['tone'] === 'red')
                                <span class="flex items-center justify-center w-6 h-6 rounded-md bg-error-container text-error">
                                    <x-rune-icon name="indicators-circle-alert" class="w-4 h-4" />
                                </span>
                            @endif
                            <h2 class="font-title-sm text-title-sm text-on-surface font-bold tracking-tight">{{ $group['title'] }}</h2>
                            <span class="px-2 py-0.5 rounded-full font-label-default text-label-default font-semibold {{ $group['tone'] === 'red' ? 'bg-error text-on-error' : 'bg-surface-container-high text-primary' }}">{{ $group['tasks']->count() }}</span>
                        </div>
                        @if ($group['tone'] === 'red')
                            <span class="font-body-sm text-body-sm text-error font-medium">Priority Escalation</span>
                        @endif
                    </div>

                    @foreach ($group['tasks'] as $task)
                        @include('livewire.partials.task-row', ['task' => $task])
                    @endforeach
                </div>
                @endif
            @endforeach
        @else
            <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-xl text-center">
                <p class="font-body-medium text-body-medium text-on-surface font-semibold">No tasks here</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Try changing the tab, filter, or search keyword.</p>
            </div>
        @endif
    </div>
</div>
