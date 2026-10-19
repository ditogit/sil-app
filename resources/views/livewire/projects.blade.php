<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1600px] w-full mx-auto">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-space-xxs min-w-0">
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Projects</h1>
            <p class="font-body-default text-body-default text-on-surface-variant">Collaborative workspace: track progress, manage milestones, and archive completed projects.</p>
        </div>
        <div class="flex items-center gap-space-sm shrink-0">
            <div class="relative w-full sm:w-64">
                <x-rune-icon name="tools-search" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                <input wire:model.live.debounce.300ms="q" type="text" placeholder="Search projects..."
                    class="w-full h-9 pl-8 pr-space-sm bg-surface-container-lowest text-on-surface placeholder:text-outline rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm transition-all" />
            </div>
            @if ($canCreate)
                <button wire:click="openQuickCreate" type="button" class="flex items-center gap-1.5 h-9 px-space-md bg-secondary text-on-secondary hover:bg-secondary-container transition-colors rounded-xl font-body-medium text-body-medium shadow-sm shrink-0 cursor-pointer">
                    <x-rune-icon name="indicators-plus" class="w-[18px] h-[18px]" />
                    <span>Create Project</span>
                </button>
                <livewire:quick-create-project />
            @endif
        </div>
    </div>

    <div class="flex items-center gap-space-xs">
        @foreach (['active' => 'Active', 'archived' => 'Archived'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')" type="button"
                class="px-space-md py-1.5 rounded-lg font-body-medium text-body-medium transition-all flex items-center gap-1.5 cursor-pointer {{ $tab === $key ? 'bg-surface-container text-secondary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                {{ $label }}
                <span class="px-1.5 py-0.5 rounded-full font-label-default text-label-default leading-none {{ $tab === $key ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant' }}">{{ $key === 'active' ? $activeCount : $archivedCount }}</span>
            </button>
        @endforeach
    </div>

    @php
        $tints = ['bg-secondary-fixed text-on-secondary-fixed', 'bg-tertiary-fixed text-on-tertiary-fixed', 'bg-surface-container-high text-primary', 'bg-surface-tint text-surface'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-space-md">
        @forelse ($projects as $project)
            @php
                $total = $project->tasks_count;
                $done = $project->done_tasks_count;
                $badge = $project->status === 'archived'
                    ? ['bg-surface-container text-on-surface-variant', 'Archived']
                    : ($project->progress >= 70
                        ? ['bg-tertiary-fixed/30 text-on-tertiary-fixed-variant', 'Active • On Track']
                        : ($project->progress >= 40 ? ['bg-surface-container-high text-secondary', 'In Review'] : ['bg-secondary-fixed text-on-secondary-fixed-variant', 'Needs Attention']));
            @endphp
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="flex flex-col gap-space-xs">
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-full {{ $badge[0] }} font-label-default text-label-default font-semibold">{{ $badge[1] }}</span>
                        @if ($canArchive && $tab === 'active')
                            <button wire:click="archive({{ $project->id }})" wire:confirm="Archive this project?" type="button" title="Archive" class="text-outline hover:text-error transition-colors p-1 cursor-pointer">
                                <x-rune-icon name="documents-archive" class="w-[18px] h-[18px]" />
                            </button>
                        @elseif ($canArchive && $tab === 'archived')
                            <button wire:click="unarchive({{ $project->id }})" type="button" title="Reactivate" class="text-outline hover:text-secondary transition-colors p-1 cursor-pointer">
                                <x-rune-icon name="schedule-refresh-cw" class="w-[18px] h-[18px]" />
                            </button>
                        @endif
                    </div>
                    <a href="{{ route('projects.show', $project) }}" class="font-title-sm text-title-sm text-on-surface font-semibold group-hover:text-secondary transition-colors mt-1">{{ $project->name }}</a>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $project->description ?: 'No description.' }}</p>
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
                                <div class="w-7 h-7 rounded-full {{ $tints[($member->id + $i) % 4] }} flex items-center justify-center font-label-default text-label-default font-bold shadow-sm" title="{{ $member->name }}">
                                    {{ $member->initials() }}
                                </div>
                            @endforeach
                            @if ($project->members->count() > 4)
                                <div class="w-7 h-7 rounded-full bg-surface-container-low text-on-surface-variant flex items-center justify-center font-label-default text-label-default font-medium shadow-sm">
                                    +{{ $project->members->count() - 4 }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="text-xs text-secondary font-medium hover:underline">Open workspace</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-surface-container-lowest p-space-xl rounded-xl shadow-sm text-center">
                <p class="font-body-medium text-body-medium text-on-surface font-semibold">{{ $tab === 'archived' ? 'No archived projects' : 'No projects yet' }}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $q !== '' ? 'Try another keyword.' : 'Create a new project to get started.' }}</p>
            </div>
        @endforelse
    </div>
</div>
