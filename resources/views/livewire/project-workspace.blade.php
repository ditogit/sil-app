<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1600px] w-full mx-auto">
    {{-- Project header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex items-center gap-space-md min-w-0">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                <x-rune-icon name="tools-house" class="w-6 h-6" />
            </div>
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-space-sm flex-wrap">
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight truncate">{{ $project->name }}</h1>
                    <span class="px-2 py-0.5 rounded-full font-label-default text-label-default font-semibold {{ $project->status === 'archived' ? 'bg-surface-container text-on-surface-variant' : 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant' }}">
                        {{ $project->status === 'archived' ? 'Archived' : 'Active' }}
                    </span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant truncate">
                    {{ $project->start_date->locale('en')->translatedFormat('d M Y') }} — {{ $project->end_date ? $project->end_date->locale('en')->translatedFormat('d M Y') : 'no end date' }}
                    • {{ $project->members_count ?? $project->members->count() }} members
                </p>
            </div>
        </div>
        <div class="flex items-center gap-space-xs shrink-0">
            @if ($canManageMembers)
            <button wire:click="setTab('pengaturan')" type="button" title="Manage members" class="flex items-center gap-1 h-9 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors cursor-pointer">
                <x-rune-icon name="identity-user-plus" class="w-[18px] h-[18px]" />
                <span class="hidden sm:inline">Members</span>
            </button>
            @endif
            @if ($canCreateTask && $project->status !== 'archived')
            <a href="{{ route('tasks.create', ['project' => $project->id]) }}" class="flex items-center gap-1 h-9 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors">
                <x-rune-icon name="documents-file-plus" class="w-[18px] h-[18px]" />
                <span class="hidden sm:inline">Add Task</span>
            </a>
            @endif
            <button type="button" title="Share link" x-data x-on:click="navigator.clipboard?.writeText(window.location.href)"
                class="w-9 h-9 flex items-center justify-center bg-surface-container-lowest rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors">
                <x-rune-icon name="tools-share-2" class="w-[18px] h-[18px]" />
            </button>
            @if ($canEdit)
            <button wire:click="setTab('pengaturan')" type="button" title="Project Settings" class="w-9 h-9 flex items-center justify-center bg-surface-container-lowest rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container shadow-sm transition-colors">
                <x-rune-icon name="tools-settings" class="w-[18px] h-[18px]" />
            </button>
            @endif
            @if ($canArchive)
            <button wire:click="archive" wire:confirm="{{ $project->status === 'archived' ? 'Reactivate this project?' : 'Archive this project?' }}" type="button" title="{{ $project->status === 'archived' ? 'Reactivate' : 'Archive project' }}"
                class="w-9 h-9 flex items-center justify-center {{ $project->status === 'archived' ? 'bg-surface-container-lowest text-on-surface-variant hover:text-secondary' : 'bg-surface-container-low text-error hover:bg-error-container' }} rounded-xl transition-colors">
                <x-rune-icon name="{{ $project->status === 'archived' ? 'schedule-refresh-cw' : 'documents-archive' }}" class="w-[18px] h-[18px]" />
            </button>
            @endif
        </div>
    </div>

    {{-- Tab --}}
    <div class="flex items-center gap-space-xs overflow-x-auto">
        @foreach (['ringkasan' => ['metrics-chart-line', 'Overview & Progress', null], 'daftar' => ['tools-list-filter', 'Task List', $allTasks->count()], 'milestone' => ['code-git-branch', 'Milestone & Roadmap', $milestones->count()], 'dokumen' => ['documents-paperclip', 'Documents & Attachments', $recentAttachments->count()], 'log' => ['schedule-history', 'Activity Log', null]] as $key => [$icon, $label, $badge])
        <button wire:click="setTab('{{ $key }}')" type="button"
            class="flex items-center gap-space-xs px-space-md py-2 rounded-xl font-body-medium text-body-medium transition-all whitespace-nowrap cursor-pointer {{ $tab === $key ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
            <x-rune-icon name="{{ $icon }}" class="w-[18px] h-[18px]" />
            <span>{{ $label }}</span>
            @if ($badge !== null)<span class="px-1.5 py-0.5 bg-surface-container text-on-surface font-label-default text-label-default rounded-full">{{ $badge }}</span>@endif
        </button>
        @endforeach
        <a href="{{ route('board', ['project' => $project->id]) }}" class="flex items-center gap-space-xs px-space-md py-2 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-body-medium text-body-medium transition-colors whitespace-nowrap">
            <x-rune-icon name="layouts-kanban" class="w-[18px] h-[18px]" />
            <span>Task Board</span>
        </a>
    </div>

    @if ($tab === 'ringkasan')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-lg">
        <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col justify-between gap-space-md">
            <div class="flex items-center justify-between">
                <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Completion Rate</span>
                <x-rune-icon name="metrics-chart-pie" class="text-secondary w-5 h-5" />
            </div>
            <div class="flex items-baseline gap-space-sm">
                <span class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">{{ $project->progress }}%</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $doneTasks->count() }} of {{ $allTasks->count() }} tasks done</span>
            </div>
            <div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden flex">
                <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: {{ $project->progress }}%;"></div>
            </div>
        </div>
        <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col justify-between gap-space-md">
            <div class="flex items-center justify-between">
                <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Workload Distribution</span>
                <x-rune-icon name="metrics-chart-bar" class="text-primary w-5 h-5" />
            </div>
            <div class="flex items-baseline gap-space-sm">
                <span class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">{{ $allTasks->count() }}</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">total tasks</span>
            </div>
            @php
            $t = max(1, $allTasks->count());
            $segDone = $doneTasks->count() / $t * 100;
            $segActive = $openTasks->count() / $t * 100;
            @endphp
            <div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden flex gap-0.5">
                <div class="bg-on-tertiary-container h-full" style="width: {{ $segDone }}%;" title="{{ $doneTasks->count() }} Completed"></div>
                <div class="bg-secondary-container h-full" style="width: {{ $segActive }}%;" title="{{ $openTasks->count() }} Active"></div>
            </div>
        </div>
        <div class="p-space-lg {{ $overdueCount > 0 ? 'bg-error-container/40' : 'bg-surface-container-lowest' }} rounded-xl shadow-sm flex flex-col justify-between gap-space-md">
            <div class="flex items-center justify-between">
                <span class="font-label-uppercase text-label-uppercase {{ $overdueCount > 0 ? 'text-error font-bold' : 'text-outline' }} uppercase tracking-wider">Overdue</span>
                <x-rune-icon name="indicators-triangle-alert" class="{{ $overdueCount > 0 ? 'text-error' : 'text-outline' }} w-5 h-5" />
            </div>
            <div class="flex items-baseline gap-space-sm">
                <span class="font-headline-xl text-headline-xl {{ $overdueCount > 0 ? 'text-error' : 'text-on-surface' }} font-bold tracking-tight">{{ $overdueCount }}</span>
                <span class="font-body-sm text-body-sm {{ $overdueCount > 0 ? 'text-error font-medium' : 'text-on-surface-variant' }}">{{ $overdueCount > 0 ? 'Act now!' : 'On time' }}</span>
            </div>
        </div>
        <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col justify-between gap-space-md">
            <div class="flex items-center justify-between">
                <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Team Members</span>
                <x-rune-icon name="identity-users" class="text-secondary w-5 h-5" />
            </div>
            <div class="flex items-baseline gap-space-sm">
                <span class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">{{ $members->count() }}</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">people in project</span>
            </div>
            <div class="flex items-center -space-x-2 overflow-hidden">
                @foreach ($members->take(6) as $row)
                <div class="w-7 h-7 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-default text-label-default font-bold shadow-sm" title="{{ $row['user']->name }}">{{ $row['user']->initials() }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        <div class="xl:col-span-8 flex flex-col gap-space-md">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-error"></span>
                <h2 class="font-headline-md text-headline-md text-on-surface">Critical Tasks In Progress</h2>
            </div>
            @forelse ($criticalTasks as $task)
            @include('livewire.partials.task-row', ['task' => $task, 'canDelete' => $canDeleteTask])
            @empty
            <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md text-center font-body-sm text-body-sm text-on-surface-variant">No critical or overdue tasks. Good job.</div>
            @endforelse
        </div>
        <div class="xl:col-span-4 flex flex-col gap-space-lg min-w-0">
            <section class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
                <div class="flex items-center gap-2">
                    <x-rune-icon name="metrics-activity" class="text-secondary w-5 h-5" />
                    <h3 class="font-headline-md text-headline-md text-on-surface">Team Workload</h3>
                </div>
                @foreach ($members as $row)
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between font-body-sm text-body-sm">
                        <span class="font-medium text-on-surface truncate">{{ $row['user']->name }}</span>
                        <span class="font-bold {{ $row['count'] >= 7 ? 'text-error' : 'text-on-surface' }}">{{ $row['count'] }} task</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="{{ $row['count'] >= 7 ? 'bg-error' : 'bg-secondary' }} h-full rounded-full" style="width: {{ min(100, (int) round($row['count'] / 8 * 100)) }}%;"></div>
                    </div>
                </div>
                @endforeach
            </section>
            <section class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
                <div class="flex items-center gap-2">
                    <x-rune-icon name="schedule-history" class="text-secondary w-5 h-5" />
                    <h3 class="font-headline-md text-headline-md text-on-surface">Recent Activity</h3>
                </div>
                @forelse ($recentComments as $comment)
                <div class="flex flex-col border-b border-surface-container-low last:border-0 pb-2 last:pb-0">
                    <p class="font-body-sm text-body-sm text-on-surface"><strong class="font-semibold">{{ $comment->author->name }}</strong> commented on <span class="text-secondary">{{ $comment->task->title }}</span></p>
                    <span class="font-label-default text-label-default text-outline mt-0.5">{{ $comment->created_at->locale('en')->diffForHumans() }}</span>
                </div>
                @empty
                <p class="font-body-sm text-body-sm text-outline">No activity yet.</p>
                @endforelse
            </section>
        </div>
    </div>
    @endif

    @if ($tab === 'daftar')
    <div class="flex flex-col gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
        <div class="flex items-center gap-space-xs">
            @foreach ([false => 'All Tasks', true => 'Only My Team'] as $value => $label)
            <button wire:click="$set('mineOnly', {{ $value ? 'true' : 'false' }})" type="button"
                class="px-space-md py-1.5 rounded-lg font-body-medium text-body-medium transition-all {{ $mineOnly === $value ? 'bg-surface-container text-secondary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">{{ $label }}</button>
            @endforeach
            <span class="ml-auto font-label-default text-label-default text-outline">{{ $daftarTasks->count() }} task</span>
        </div>
    </div>
    <div class="flex flex-col gap-space-xs">
        @forelse ($daftarTasks as $task)
        @include('livewire.partials.task-row', ['task' => $task, 'canDelete' => $canDeleteTask])
        @empty
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-xl text-center font-body-sm text-body-sm text-on-surface-variant">No tasks in this project.</div>
        @endforelse
    </div>
    @endif

    @if ($tab === 'milestone')
    <div class="flex flex-col gap-space-md">
        <div class="flex items-center justify-between">
            <h2 class="font-headline-md text-headline-md text-on-surface">Milestone &amp; Delivery Roadmap</h2>
            @if ($canEdit)
            <span class="font-label-default text-label-default text-outline">{{ $milestones->count() }} milestone</span>
            @endif
        </div>
        @if ($canEdit)
        <form wire:submit="addMilestone" class="flex flex-col sm:flex-row gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
            <input wire:model="milestoneTitle" type="text" maxlength="255" placeholder="New milestone title..."
                class="flex-1 h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('milestoneTitle') ring-2 ring-error @enderror" />
            <input wire:model="milestoneDue" type="date"
                class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
            <button type="submit" class="h-10 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors shrink-0">Add</button>
        </form>
        @error('milestoneTitle')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
        @endif
        <div class="flex flex-col gap-space-sm">
            @forelse ($milestones as $milestone)
            @php
            $mTotal = $milestone->tasks_count;
            $mDone = $milestone->done_tasks_count;
            $mPct = $mTotal > 0 ? (int) round($mDone / $mTotal * 100) : 0;
            @endphp
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
                @if ($editingMilestoneId === $milestone->id)
                <form wire:submit="saveMilestone" class="flex flex-col sm:flex-row gap-space-sm">
                    <input wire:model="editingMilestoneTitle" type="text" maxlength="255" class="flex-1 h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                    <input wire:model="editingMilestoneDue" type="date" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                    <button type="submit" class="h-10 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors shrink-0">Save</button>
                    <button wire:click="$set('editingMilestoneId', null)" type="button" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shrink-0">Cancel</button>
                </form>
                @else
                <div class="flex items-center justify-between gap-space-md">
                    <div class="flex items-center gap-space-sm min-w-0">
                        <x-rune-icon name="tools-flag" class="text-secondary w-5 h-5 shrink-0" />
                        <h3 class="font-title-sm text-title-sm text-on-surface font-semibold truncate">{{ $milestone->title }}</h3>
                        @if ($milestone->due_date)
                        <span class="font-label-default text-label-default text-outline whitespace-nowrap">Due date {{ $milestone->due_date->locale('en')->translatedFormat('d M Y') }}</span>
                        @endif
                    </div>
                    @if ($canEdit)
                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="startEditMilestone({{ $milestone->id }})" type="button" title="Edit" class="p-1.5 text-on-surface-variant hover:text-secondary rounded-lg hover:bg-surface-container transition-colors">
                            <x-rune-icon name="tools-pencil" class="w-[18px] h-[18px]" />
                        </button>
                        <button wire:click="deleteMilestone({{ $milestone->id }})" wire:confirm="Delete this milestone? Its tasks will have no milestone." type="button" title="Delete" class="p-1.5 text-on-surface-variant hover:text-error rounded-lg hover:bg-error-container transition-colors">
                            <x-rune-icon name="tools-trash" class="w-[18px] h-[18px]" />
                        </button>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-space-sm">
                    <div class="flex-1 bg-surface-container h-2 rounded-full overflow-hidden">
                        <div class="bg-secondary h-full rounded-full transition-all" style="width: {{ $mPct }}%;"></div>
                    </div>
                    <span class="font-label-default text-label-default text-on-surface-variant whitespace-nowrap">{{ $mDone }}/{{ $mTotal }} ({{ $mPct }}%)</span>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-xl text-center font-body-sm text-body-sm text-on-surface-variant">No milestones yet. Add a project delivery milestone above.</div>
            @endforelse
        </div>
    </div>
    @endif

    @if ($tab === 'dokumen')
    <div class="flex flex-col gap-space-md">
        <h2 class="font-headline-md text-headline-md text-on-surface">Documents &amp; Attachments ({{ $recentAttachments->count() }} latest)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            @forelse ($recentAttachments as $attachment)
            <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-10 h-10 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                    <x-rune-icon name="documents-file-text" class="w-[22px] h-[22px]" />
                </div>
                <div class="flex-1 min-w-0 leading-tight">
                    <p class="font-body-medium text-body-medium text-on-surface font-medium truncate" title="{{ $attachment->file_name }}">{{ $attachment->file_name }}</p>
                    <span class="font-label-default text-label-default text-outline">{{ $attachment->task->title }} • {{ $attachment->uploader?->name }}</span>
                </div>
                <a href="{{ route('attachments.download', [$attachment->task, $attachment]) }}" title="Download" class="p-1 text-on-surface-variant hover:text-secondary rounded transition-colors">
                    <x-rune-icon name="documents-file-down" class="w-[18px] h-[18px]" />
                </a>
            </div>
            @empty
            <p class="font-body-sm text-body-sm text-outline sm:col-span-2">No documents in this project yet.</p>
            @endforelse
        </div>
    </div>
    @endif

    @if ($tab === 'log')
    <div class="flex flex-col gap-space-md">
        <h2 class="font-headline-md text-headline-md text-on-surface">Team Activity Log</h2>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md relative before:absolute before:top-4 before:bottom-4 before:left-[42px] before:w-0.5 before:bg-surface-container">
            @forelse ($recentComments as $comment)
            <div class="flex items-start gap-space-md relative">
                <div class="w-7 h-7 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant flex items-center justify-center text-[11px] font-bold shrink-0 z-10">{{ $comment->author->initials() }}</div>
                <div class="flex flex-col">
                    <p class="font-body-sm text-body-sm text-on-surface"><strong class="font-semibold">{{ $comment->author->name }}</strong> commented on <span class="text-secondary">{{ $comment->task->title }}</span></p>
                    <span class="font-label-default text-label-default text-outline mt-1">{{ $comment->created_at->locale('en')->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p class="font-body-sm text-body-sm text-outline">No activity yet.</p>
            @endforelse
        </div>
    </div>
    @endif

    @if ($tab === 'pengaturan')
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-space-lg items-start">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
            <h2 class="font-headline-md text-headline-md text-on-surface">Project Settings</h2>
            @if ($canEdit)
            <div class="flex flex-col gap-space-sm">
                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="projectName">Project Name</label>
                <input wire:model="name" id="projectName" type="text" maxlength="255" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                @error('name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="projectDesc">Description</label>
                <textarea wire:model="description" id="projectDesc" rows="3" class="p-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none resize-y focus:ring-2 focus:ring-secondary/25"></textarea>
                <div class="grid grid-cols-2 gap-space-sm">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Start</label>
                        <input wire:model="start_date" type="date" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">End</label>
                        <input wire:model="end_date" type="date" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                    </div>
                </div>
                @error('start_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                @error('end_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                <button wire:click="saveProject" type="button" class="h-10 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors w-fit">Save Changes</button>
            </div>
            @else
            <p class="font-body-sm text-body-sm text-on-surface-variant">Only Project Managers and Admins can edit the project.</p>
            @endif
            @if ($canArchive)
            <div class="pt-space-md border-t border-surface-container-low flex flex-col gap-space-sm">
                <span class="font-label-uppercase text-label-uppercase text-error uppercase tracking-wider font-semibold">Danger Zone</span>
                <button wire:click="archive" wire:confirm="{{ $project->status === 'archived' ? 'Reactivate this project?' : 'Archive this project? Archived projects are hidden from the active list.' }}" type="button"
                    class="h-10 px-space-md rounded-xl font-body-medium text-body-medium transition-colors w-fit {{ $project->status === 'archived' ? 'bg-secondary text-on-secondary hover:bg-secondary-container' : 'bg-error-container/50 text-error hover:bg-error-container' }}">
                    {{ $project->status === 'archived' ? 'Reactivate Project' : 'Archive Project' }}
                </button>
            </div>
            @endif
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <h2 class="font-headline-md text-headline-md text-on-surface">Members ({{ $members->count() }})</h2>
            </div>
            @if ($canManageMembers)
            <form wire:submit="addMember" class="flex gap-space-sm">
                <select wire:model="new_member_id" class="flex-1 h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25">
                    <option value="">— Select new member —</option>
                    @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} — {{ str($user->getRoleNames()->first() ?? 'member')->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-10 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors shrink-0">Add</button>
            </form>
            @error('new_member_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
            @error('members')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
            @endif
            <div class="flex flex-col gap-space-xs">
                @foreach ($members as $row)
                <div class="flex items-center justify-between p-space-sm rounded-xl bg-surface-container-low">
                    <div class="flex items-center gap-space-sm min-w-0">
                        <div class="w-8 h-8 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-default text-label-default font-bold shrink-0">{{ $row['user']->initials() }}</div>
                        <div class="flex flex-col min-w-0 leading-tight">
                            <span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">{{ $row['user']->name }}</span>
                            <span class="font-label-default text-label-default text-outline">{{ str($row['user']->getRoleNames()->first() ?? 'member')->replace('_', ' ')->title() }} • {{ $row['count'] }} active tasks</span>
                        </div>
                    </div>
                    @if ($canManageMembers && $row['user']->id !== $project->created_by)
                    <button wire:click="removeMember({{ $row['user']->id }})" wire:confirm="Remove this member from the project?" type="button" title="Remove" class="p-1.5 text-on-surface-variant hover:text-error rounded-lg hover:bg-error-container transition-colors shrink-0">
                        <x-rune-icon name="indicators-x" class="w-[18px] h-[18px]" />
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
