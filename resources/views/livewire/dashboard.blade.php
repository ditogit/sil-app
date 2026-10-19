<div class="px-space-xl py-space-lg flex flex-col gap-space-xl max-w-[1600px] mx-auto w-full">
    {{-- Header --}}
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
        <div class="flex flex-col gap-1 min-w-0">
            <div class="flex items-center gap-space-sm flex-wrap">
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight flex items-center gap-2">
                    {{ $greeting }}, {{ $userName }}!
                    <span aria-label="Greeting" class="inline-flex items-center justify-center text-lg select-none">👋</span>
                </h1>
                @if ($userRole)
                    <span class="px-2.5 py-1 rounded-full bg-surface-container-high text-primary font-label-default text-label-default flex items-center gap-1.5 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                        {{ str($userRole)->replace('_', ' ')->title() }}
                    </span>
                @endif
            </div>
            <p class="font-body-default text-body-default text-on-surface-variant">
                Monitor tasks, projects, and your team's productivity in one place.
            </p>
            <div class="flex items-center gap-space-xs mt-1 text-outline font-label-default text-label-default">
                <x-rune-icon name="schedule-calendar" class="w-3.75 h-[15px] text-secondary" />
                <span class="text-on-surface font-medium">{{ $todayLabel }}</span>
                <span class="text-outline-variant">•</span>
                <span>{{ $activeProjectCount }} active projects</span>
            </div>
        </div>
        <div class="flex items-center gap-space-sm flex-wrap lg:self-center shrink-0">
            <button wire:click="cyclePeriod" type="button" class="flex items-center gap-2 h-9 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-medium text-body-medium shadow-sm hover:bg-surface-container-low transition-colors">
                <x-rune-icon name="metrics-sliders-horizontal" class="w-[18px] h-[18px] text-on-surface-variant" />
                <span>Filter: <strong class="font-semibold text-secondary">{{ $periodLabel }}</strong></span>
                <x-rune-icon name="arrows-chevron-down" class="w-[16px] h-[16px] text-outline" />
            </button>
            @if ($canCreateTask)
                <a href="{{ route('tasks.create') }}" class="flex items-center gap-space-xs h-9 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium hover:bg-secondary-container transition-all shadow-sm active:scale-95">
                    <x-rune-icon name="documents-file-plus" class="w-[18px] h-[18px]" />
                    <span class="font-semibold">+ Create Task</span>
                </a>
            @endif
        </div>
    </section>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-secondary/5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-default text-label-default text-on-surface-variant uppercase tracking-wider font-semibold">Total Active Tasks</span>
                    <div class="font-headline-xl text-headline-xl text-on-surface mt-1 font-bold tracking-tight">{{ $totalActive }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary">
                    <x-rune-icon name="documents-clipboard-list" class="w-[22px] h-[22px]" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-on-surface-variant font-body-sm text-body-sm">
                <span class="flex items-center font-semibold px-1.5 py-0.5 rounded text-[11px] {{ $newDelta >= 0 ? 'text-on-tertiary-fixed-variant bg-tertiary-fixed/30' : 'text-on-error-container bg-error-container/60' }}">
                    <x-rune-icon name="{{ $newDelta >= 0 ? 'metrics-trending-up' : 'arrows-arrow-down' }}" class="w-[14px] h-[14px]" />
                    {{ ($newDelta >= 0 ? '+' : '').$newDelta }}
                </span>
                <span class="truncate">new tasks vs previous period ({{ strtolower($periodLabel) }})</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-error/5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-default text-label-default text-on-surface-variant uppercase tracking-wider font-semibold">Task Overdue</span>
                    <div class="font-headline-xl text-headline-xl text-error mt-1 font-bold tracking-tight">{{ $overdueCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-error-container/40 flex items-center justify-center text-error">
                    <x-rune-icon name="indicators-circle-x" class="w-[22px] h-[22px]" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                @if ($overdueCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-default text-label-default font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-error animate-pulse"></span>
                        Needs immediate action
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed/40 text-on-tertiary-fixed-variant font-label-default text-label-default font-semibold">
                        All on time
                    </span>
                @endif
            </div>
        </div>
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-tertiary/5 rounded-full blur-xl group-hover:scale-125 transition-transform"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-default text-label-default text-on-surface-variant uppercase tracking-wider font-semibold">Completed {{ $periodLabel }}</span>
                    <div class="font-headline-xl text-headline-xl text-on-surface mt-1 font-bold tracking-tight">{{ $doneInPeriod }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-on-tertiary-fixed-variant">
                    <x-rune-icon name="identity-shield-check" class="w-[22px] h-[22px]" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-on-surface-variant font-body-sm text-body-sm">
                <span class="truncate">tasks with done status in this period</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-default text-label-default text-on-surface-variant uppercase tracking-wider font-semibold">Project Progress</span>
                    <div class="font-headline-xl text-headline-xl text-on-surface mt-1 font-bold tracking-tight">{{ $avgProgress }}%</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center text-primary-container">
                    <x-rune-icon name="layouts-kanban" class="w-[22px] h-[22px]" />
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-1.5">
                <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-container to-secondary h-full rounded-full transition-all duration-500" style="width: {{ $avgProgress }}%;"></div>
                </div>
                <div class="flex items-center justify-between text-outline font-label-default text-label-default">
                    <span>{{ $onTrackCount }} of {{ $activeProjectCount }} projects on-track</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Main grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        <div class="xl:col-span-8 flex flex-col gap-space-lg min-w-0">
            {{-- My Tasks --}}
            <section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col" x-data="{ all: false }">
                <div class="p-space-md flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm bg-surface-container-lowest">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                        <h2 class="font-headline-md text-headline-md text-on-surface">My Tasks</h2>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-default text-label-default font-semibold">
                            {{ $myActiveCount }} Assigned
                        </span>
                    </div>
                    <div class="flex items-center gap-space-xs self-end sm:self-auto">
                        <button wire:click="cyclePriorityFilter" type="button" class="px-2.5 py-1.5 rounded-lg text-secondary bg-surface-container-low font-body-sm text-body-sm font-medium hover:bg-surface-container transition-colors">
                            {{ $priorityFilterLabel }}
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left font-body-sm text-body-sm min-w-[700px]">
                        <thead class="bg-surface-container-low text-on-surface-variant font-label-uppercase text-label-uppercase uppercase">
                            <tr>
                                <th class="py-3 px-space-md w-10 text-center" scope="col">
                                    <input x-model="all" x-on:change="$el.closest('table').querySelectorAll('.task-checkbox').forEach(cb => cb.checked = all)" type="checkbox" class="rounded accent-secondary w-4 h-4 cursor-pointer" />
                                </th>
                                <th class="py-3 px-space-md" scope="col">Task &amp; Subtext</th>
                                <th class="py-3 px-space-md" scope="col">Project</th>
                                <th class="py-3 px-space-md" scope="col">Priority</th>
                                <th class="py-3 px-space-md" scope="col">Due Date</th>
                                <th class="py-3 px-space-md" scope="col">Status</th>
                                <th class="py-3 px-space-md text-right" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse ($myTasks as $task)
                                @php
                                    $isOverdue = $task->due_date->lt(today());
                                    $priorityPill = match ($task->priority) {
                                        'critical' => ['bg-error text-on-error', 'bg-on-error', 'Critical'],
                                        'high' => ['bg-error-container text-on-error-container', 'bg-error', 'High'],
                                        'medium' => ['bg-surface-container text-on-surface-variant', null, 'Medium'],
                                        default => ['bg-surface-container-low text-outline', null, 'Low'],
                                    };
                                    $statusPill = match ($task->status) {
                                        'in_progress' => ['bg-surface-container-high text-secondary', 'In Progress'],
                                        'review' => ['bg-surface-container-high text-primary', 'Review'],
                                        default => ['bg-surface-container text-on-surface-variant', 'To Do'],
                                    };
                                    $nextStatus = match ($task->status) {
                                        'todo' => 'In Progress',
                                        'in_progress' => 'Review',
                                        default => 'Done',
                                    };
                                @endphp
                                <tr class="hover:bg-surface-container-low/60 transition-colors group">
                                    <td class="py-3 px-space-md text-center">
                                        <input type="checkbox" class="task-checkbox rounded accent-secondary w-4 h-4 cursor-pointer" />
                                    </td>
                                    <td class="py-3 px-space-md max-w-[240px]">
                                        <div class="flex flex-col">
                                            <span class="font-body-medium text-body-medium font-semibold text-on-surface group-hover:text-secondary transition-colors truncate">{{ $task->title }}</span>
                                            <span class="font-label-default text-label-default text-outline truncate">{{ $task->project->name }}{{ $task->milestone ? ' • '.$task->milestone->title : '' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-space-md whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant font-label-default text-label-default">{{ $task->project->name }}</span>
                                    </td>
                                    <td class="py-3 px-space-md whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $priorityPill[0] }} font-label-default text-label-default font-semibold">
                                            @if ($priorityPill[1])<span class="w-1.5 h-1.5 rounded-full {{ $priorityPill[1] }}"></span>@endif
                                            {{ $priorityPill[2] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-space-md whitespace-nowrap">
                                        @if ($isOverdue)
                                            <span class="text-error font-medium flex items-center gap-1">
                                                <x-rune-icon name="schedule-clock-alert" class="w-[15px] h-[15px]" />
                                                {{ $task->due_date->locale('en')->translatedFormat('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-on-surface font-medium">{{ $task->due_date->isToday() ? 'Today' : ($task->due_date->isTomorrow() ? 'Tomorrow' : $task->due_date->locale('en')->translatedFormat('d M Y')) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-space-md whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full {{ $statusPill[0] }} font-label-default text-label-default font-semibold">{{ $statusPill[1] }}</span>
                                    </td>
                                    <td class="py-3 px-space-md text-right whitespace-nowrap">
                                        @if ($task->canUpdateStatus)
                                            <button wire:click="advanceStatus({{ $task->id }})" title="Advance to {{ $nextStatus }}" type="button" class="p-1.5 rounded-lg text-secondary hover:bg-surface-container-low transition-colors">
                                                <x-rune-icon name="arrows-skip-forward" class="w-[18px] h-[18px]" />
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 px-space-md text-center text-on-surface-variant font-body-sm text-body-sm">
                                        No active tasks for you{{ $priorityFilter !== 'all' ? ' with this priority' : '' }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-space-sm bg-surface-container-low/50 flex items-center justify-between">
                    <span class="font-label-default text-label-default text-outline">
                        Showing {{ $myTasks->count() }} of {{ $myActiveCount }} of your active tasks
                    </span>
                    @if ($myActiveCount > 5)
                        <button wire:click="$set('showAllMyTasks', {{ $showAllMyTasks ? 'false' : 'true' }})" type="button" class="inline-flex items-center gap-1 text-secondary font-body-sm text-body-sm font-semibold hover:underline">
                            {{ $showAllMyTasks ? 'Show less' : "View All My Tasks ($myActiveCount)" }}
                            <x-rune-icon name="{{ $showAllMyTasks ? 'arrows-chevron-up' : 'arrows-arrow-right' }}" class="w-[16px] h-[16px]" />
                        </button>
                    @endif
                </div>
            </section>

            {{-- Project Progress --}}
            <section class="flex flex-col gap-space-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Featured Project Progress</h2>
                    </div>
                    @if ($projectsTotal > 3)
                        <button wire:click="$set('showAllProjects', {{ $showAllProjects ? 'false' : 'true' }})" type="button" class="text-secondary font-body-sm text-body-sm font-medium hover:underline flex items-center gap-1">
                            {{ $showAllProjects ? 'Show less' : "All Projects ($projectsTotal)" }}
                            <x-rune-icon name="arrows-chevron-right" class="w-[16px] h-[16px]" />
                        </button>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                    @php
                        $avatarTints = ['bg-secondary-fixed text-on-secondary-fixed', 'bg-tertiary-fixed text-on-tertiary-fixed', 'bg-surface-container-high text-primary', 'bg-surface-tint text-surface'];
                    @endphp
                    @forelse ($projects as $project)
                        @php
                            $total = $project->tasks->count();
                            $done = $project->tasks->where('status', 'done')->count();
                            $badge = $project->progress >= 70
                                ? ['bg-tertiary-fixed/30 text-on-tertiary-fixed-variant', 'bg-on-tertiary-fixed-variant', 'Active • On Track']
                                : ($project->progress >= 40
                                    ? ['bg-surface-container-high text-secondary', 'bg-secondary', 'In Review']
                                    : ['bg-secondary-fixed text-on-secondary-fixed-variant', 'bg-secondary-container', 'Needs Attention']);
                        @endphp
                        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                            <div class="flex flex-col gap-space-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="px-2 py-0.5 rounded-full {{ $badge[0] }} font-label-default text-label-default font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                                        {{ $badge[2] }}
                                    </span>
                                </div>
                                <h3 class="font-title-sm text-title-sm text-on-surface font-semibold group-hover:text-secondary transition-colors mt-1"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $project->description }}</p>
                            </div>
                            <div class="mt-4 flex flex-col gap-space-sm">
                                <div class="flex items-center justify-between text-body-sm text-body-sm">
                                    <span class="text-on-surface-variant">Completion</span>
                                    <span class="font-semibold text-on-surface">{{ $project->progress }}%</span>
                                </div>
                                <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                    <div class="bg-secondary h-full rounded-full transition-all duration-300" style="width: {{ $project->progress }}%;"></div>
                                </div>
                                <div class="flex items-center justify-between pt-2 text-outline font-label-default text-label-default">
                                    <span class="flex items-center gap-1">
                                        <x-rune-icon name="indicators-circle-check" class="w-[14px] h-[14px]" /> {{ $done }}/{{ $total }} Task
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-rune-icon name="schedule-calendar" class="w-[14px] h-[14px]" />
                                        {{ $project->end_date ? $project->end_date->locale('en')->translatedFormat('d M Y') : '—' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between pt-2">
                                    <div class="flex items-center -space-x-2 overflow-hidden">
                                        @foreach ($project->members->take(4) as $i => $member)
                                            <div class="w-7 h-7 rounded-full {{ $avatarTints[$i % 4] }} flex items-center justify-center font-label-default text-label-default font-bold shadow-sm">
                                                {{ $member->initials() }}
                                            </div>
                                        @endforeach
                                        @if ($project->members->count() > 4)
                                            <div class="w-7 h-7 rounded-full bg-surface-container-low text-on-surface-variant flex items-center justify-center font-label-default text-label-default font-medium shadow-sm">
                                                +{{ $project->members->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-xs text-outline font-code-sm">{{ $project->milestones->count() }} milestone</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-3 bg-surface-container-lowest p-space-md rounded-xl shadow-sm text-center text-on-surface-variant font-body-sm text-body-sm">
                            No active projects yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Right column --}}
        <div class="xl:col-span-4 flex flex-col gap-space-lg min-w-0">
            {{-- Deadline --}}
            <section class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col">
                <div class="flex items-center justify-between pb-space-sm">
                    <div class="flex items-center gap-2">
                        <x-rune-icon name="schedule-alarm-clock" class="text-error w-[20px] h-[20px]" />
                        <h3 class="font-headline-md text-headline-md text-on-surface">Urgent Deadlines</h3>
                    </div>
                    @if ($overdueCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-default text-label-default font-bold">{{ $overdueCount }} Important</span>
                    @endif
                </div>
                <div class="flex flex-col gap-space-sm mt-space-xs">
                    @forelse ($deadlines as $task)
                        @php $isOverdue = $task->due_date->lt(today()); @endphp
                        <div class="p-space-sm rounded-xl {{ $isOverdue ? 'bg-error-container/20 hover:bg-error-container/30' : 'bg-surface-container-low hover:bg-surface-container' }} flex flex-col gap-1 transition-colors">
                            <div class="flex items-center justify-between gap-2">
                                @if ($isOverdue)
                                    <span class="px-2 py-0.5 rounded-md bg-error text-on-error font-label-default text-label-default font-semibold flex items-center gap-1">
                                        <x-rune-icon name="indicators-triangle-alert" class="w-[13px] h-[13px]" />
                                        Overdue {{ $task->due_date->diffInDays(today()) }} days
                                    </span>
                                @elseif ($task->due_date->isToday())
                                    <span class="px-2 py-0.5 rounded-md bg-surface-container-high text-secondary font-label-default text-label-default font-semibold flex items-center gap-1">
                                        <x-rune-icon name="schedule-clock" class="w-[13px] h-[13px]" /> Today
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant font-label-default text-label-default font-medium">
                                        {{ $task->due_date->locale('en')->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                                <span class="text-outline font-label-default text-label-default truncate">{{ $task->project->name }}</span>
                            </div>
                            <h4 class="font-body-medium text-body-medium font-semibold text-on-surface mt-1">{{ $task->title }}</h4>
                            <div class="flex items-center justify-between text-on-surface-variant font-label-default text-label-default pt-1">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-surface-container-high text-primary flex items-center justify-center text-[10px] font-bold">
                                        {{ $task->assignee ? $task->assignee->initials() : '–' }}
                                    </div>
                                    <span>{{ $task->assignee?->name ?? 'Not yet assigned' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-on-surface-variant font-body-sm text-body-sm">No urgent deadlines.</p>
                    @endforelse
                </div>
            </section>

            {{-- Workload --}}
            <section class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col">
                <div class="flex items-center justify-between pb-space-sm">
                    <div class="flex items-center gap-2">
                        <x-rune-icon name="metrics-chart-bar" class="text-secondary w-[20px] h-[20px]" />
                        <h3 class="font-headline-md text-headline-md text-on-surface">Team Workload</h3>
                    </div>
                    <span class="text-outline font-label-default text-label-default">{{ $workload->count() }} Members</span>
                </div>
                <div class="flex flex-col gap-space-md mt-space-xs">
                    @foreach ($workload as $row)
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center justify-between font-body-sm text-body-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-on-surface">{{ $row['user']->name }}</span>
                                    <span class="text-outline font-label-default text-label-default">{{ $row['user']->getRoleNames()->first() }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold {{ $row['isFull'] ? 'text-error' : 'text-on-surface' }}">{{ $row['count'] }} task</span>
                                    <span class="{{ $row['isFull'] ? 'text-error font-semibold' : 'text-outline' }} text-xs">({{ $row['percent'] }}%){{ $row['isFull'] ? ' Full' : '' }}</span>
                                </div>
                            </div>
                            <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                                <div class="{{ $row['isFull'] ? 'bg-error' : 'bg-secondary' }} h-full rounded-full" style="width: {{ $row['percent'] }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between bg-surface-container-low/40 p-2 rounded-lg text-outline font-label-default text-label-default">
                    <span>Normal load: &lt; 7 tasks</span>
                    <span class="text-error font-semibold">High: ≥ 7 tasks</span>
                </div>
            </section>

            {{-- Activity --}}
            <section class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col">
                <div class="flex items-center justify-between pb-space-sm">
                    <div class="flex items-center gap-2">
                        <x-rune-icon name="schedule-history" class="text-secondary w-[20px] h-[20px]" />
                        <h3 class="font-headline-md text-headline-md text-on-surface">Recent Activity</h3>
                    </div>
                </div>
                <div class="flex flex-col gap-space-md mt-space-xs relative before:absolute before:top-2 before:bottom-2 before:left-3.5 before:w-0.5 before:bg-surface-container">
                    @forelse ($activities as $activity)
                        <div class="flex items-start gap-space-md relative">
                            <div class="w-7 h-7 rounded-full {{ $activity['tint'] }} flex items-center justify-center shrink-0 z-10 shadow-sm">
                                <x-rune-icon name="{{ $activity['icon'] }}" class="w-[14px] h-[14px]" />
                            </div>
                            <div class="flex flex-col min-w-0">
                                <p class="font-body-sm text-body-sm text-on-surface leading-tight">
                                    {{ $activity['text'] }}
                                    <span class="text-secondary font-medium">"{{ $activity['task'] }}"</span>
                                </p>
                                <span class="font-label-default text-label-default text-outline mt-1">{{ $activity['time']->locale('en')->diffForHumans() }} • {{ $activity['meta'] }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-on-surface-variant font-body-sm text-body-sm">No activity yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

</div>
