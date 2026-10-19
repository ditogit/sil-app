<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1600px] w-full mx-auto print:px-0">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md print:hidden">
        <div class="flex flex-col gap-space-xxs min-w-0">
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Report &amp; Performance Analytics</h1>
            <p class="font-body-default text-body-default text-on-surface-variant">Completion trends, workload distribution, and SLA compliance recaps per project.</p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm shrink-0">
            <div class="relative">
                <select wire:model.live="period" class="h-9 pl-9 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-xl appearance-none cursor-pointer outline-none shadow-sm">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
                <x-rune-icon name="schedule-calendar" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-[18px] h-[18px]" />
                <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            </div>
            <div class="relative">
                <select wire:model.live="project_id" class="h-9 pl-9 pr-8 bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded-xl appearance-none cursor-pointer outline-none shadow-sm max-w-64">
                    <option value="all">All Active Projects</option>
                    @foreach ($projectOptions as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <x-rune-icon name="documents-folder-open" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-[18px] h-[18px]" />
                <x-rune-icon name="arrows-chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
            </div>
            @if ($canExport)
                <div x-data="{ open: false }" class="relative">
                    <button x-on:click="open = ! open" type="button" class="flex items-center gap-space-xs h-9 px-space-md bg-primary-container text-on-primary rounded-xl shadow-sm hover:bg-primary transition-all font-body-medium text-body-medium">
                        <x-rune-icon name="documents-file-down" class="w-[18px] h-[18px]" />
                        <span>Export Report</span>
                        <x-rune-icon name="arrows-chevron-down" class="w-4 h-4" />
                    </button>
                    <div x-show="open" x-cloak x-on:click.outside="open = false" class="absolute right-0 mt-1.5 z-30 w-52">
                        <div class="bg-surface-container-lowest rounded-xl shadow-xl p-1.5 flex flex-col gap-1">
                            <a href="{{ route('reports.export', ['project' => $project_id]) }}" x-on:click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container-low font-body-sm text-body-sm transition-colors">
                                <x-rune-icon name="code-braces" class="text-on-tertiary-fixed-variant w-[18px] h-[18px]" />
                                <span>Spreadsheet (.csv)</span>
                            </a>
                            <button onclick="window.print()" x-on:click="open = false" type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container-low font-body-sm text-body-sm transition-colors">
                                <x-rune-icon name="documents-file-text" class="text-error w-[18px] h-[18px]" />
                                <span>PDF Document (print)</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <p class="font-label-default text-label-default text-outline print:hidden">Period: {{ $range['label'] }} ({{ $range['start']->locale('en')->translatedFormat('d M Y') }} — {{ $range['end']->locale('en')->translatedFormat('d M Y') }})</p>

    {{-- KPI --}}
    @php
        $rate = ($createdInPeriod + $doneInPeriod) > 0 ? (int) round($doneInPeriod / max(1, $createdInPeriod) * 100) : 0;
        $openTotal = $total - $statusCounts['done'];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md">
        @foreach ([
            ['Completion Rate', $rate.'%', $doneInPeriod.' completed / '.$createdInPeriod.' created', 'metrics-trending-up', 'text-secondary'],
            ['Tasks Completed', $doneInPeriod, 'in this period', 'indicators-square-check', 'text-on-tertiary-fixed-variant'],
            ['Tasks Created', $createdInPeriod, 'in this period', 'documents-file-plus', 'text-secondary'],
            ['Open Tasks', $openTotal, 'of '.$total.' total', 'schedule-clock-alert', 'text-error'],
        ] as [$label, $value, $sub, $icon, $color])
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">{{ $label }}</span>
                    <x-rune-icon name="{{ $icon }}" class="{{ $color }} w-5 h-5" />
                </div>
                <div class="mt-1 font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">{{ $value }}</div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        {{-- Trend --}}
        <div class="xl:col-span-8 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-space-md gap-2">
                <div>
                    <h2 class="font-title-sm text-title-sm text-on-surface">Productivity Trend &amp; Task Completion Rate</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tasks created vs completed per day.</p>
                </div>
                <div class="flex items-center gap-space-md font-body-sm text-body-sm">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-primary-container"></span><span class="text-on-surface font-medium">Created</span></span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-secondary"></span><span class="text-on-surface font-medium">Completed</span></span>
                </div>
            </div>
            @php
                $maxV = max(1, max($created + [0]), max($completed + [0]));
                $n = count($labels);
                $gw = $n > 0 ? 700 / $n : 0;
                $bw = min(14, $gw * 0.32);
            @endphp
            <div class="w-full overflow-x-auto">
                <svg viewBox="0 0 760 {{ 40 + 170 }}" class="w-full min-w-[560px] h-64" preserveAspectRatio="xMidYMid meet">
                    @for ($g = 0; $g <= 4; $g++)
                        <line x1="40" x2="740" y1="{{ 10 + $g * 42 }}" y2="{{ 10 + $g * 42 }}" stroke="#E2E8F0" stroke-dasharray="3 3" stroke-width="1" />
                        <text x="32" y="{{ 14 + $g * 42 }}" text-anchor="end" font-size="11" fill="#74777F" font-family="Inter">{{ (int) round($maxV * (4 - $g) / 4) }}</text>
                    @endfor
                    @foreach ($labels as $i => $day)
                        @php
                            $x = 45 + $i * $gw;
                            $h1 = $created[$i] / $maxV * 168;
                            $h2 = $completed[$i] / $maxV * 168;
                        @endphp
                        <rect x="{{ $x }}" y="{{ 178 - $h1 }}" width="{{ $bw }}" height="{{ max($h1, 2) }}" rx="2" fill="#17365d"><title>{{ $day }}: {{ $created[$i] }} created</title></rect>
                        <rect x="{{ $x + $bw + 2 }}" y="{{ 178 - $h2 }}" width="{{ $bw }}" height="{{ max($h2, 2) }}" rx="2" fill="#0051d5"><title>{{ $day }}: {{ $completed[$i] }} completed</title></rect>
                        @if ($n <= 31 || $i % (int) ceil($n / 15) === 0)
                            <text x="{{ $x + $bw }}" y="196" text-anchor="middle" font-size="10" fill="#74777F" font-family="Inter">{{ $day }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </div>

        {{-- Status donut --}}
        <div class="xl:col-span-4 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <h2 class="font-title-sm text-title-sm text-on-surface">Distribusi Status</h2>
            @php
                $segs = [
                    ['Done', $statusCounts['done'], '#32b559'],
                    ['In Progress', $statusCounts['in_progress'], '#0051d5'],
                    ['Review', $statusCounts['review'], '#17365d'],
                    ['To Do', $statusCounts['todo'], '#c4c6cf'],
                ];
                $off = 25;
            @endphp
            <div class="flex items-center gap-space-md">
                <svg viewBox="0 0 36 36" class="w-36 h-36 shrink-0">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#e5eeff" stroke-width="4" />
                    @foreach ($segs as [$sl, $sv, $sc])
                        @php $pct = $total > 0 ? $sv / $total * 100 : 0; @endphp
                        @if ($pct > 0)
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="{{ $sc }}" stroke-width="4"
                                stroke-dasharray="{{ $pct }} {{ 100 - $pct }}" stroke-dashoffset="{{ $off }}" stroke-linecap="round">
                                <title>{{ $sl }}: {{ $sv }}</title>
                            </circle>
                            @php $off -= $pct; @endphp
                        @endif
                    @endforeach
                    <text x="18" y="20.5" text-anchor="middle" font-size="7" font-weight="700" fill="#0b1c30" font-family="Inter">{{ $total }}</text>
                </svg>
                <div class="flex flex-col gap-1.5">
                    @foreach ($segs as [$sl, $sv, $sc])
                        <span class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background: {{ $sc }}"></span>
                            {{ $sl }} <strong class="font-semibold">{{ $sv }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col gap-space-xs pt-2 border-t border-surface-container-low">
                <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Priority</span>
                @foreach ([['Critical', $priorityCounts['critical'], 'bg-error'], ['High', $priorityCounts['high'], 'bg-secondary-container'], ['Medium', $priorityCounts['medium'], 'bg-surface-container-high'], ['Low', $priorityCounts['low'], 'bg-surface-container-low']] as [$pl, $pv, $pc])
                    <div class="flex items-center gap-space-sm font-body-sm text-body-sm">
                        <span class="w-24 text-on-surface-variant">{{ $pl }}</span>
                        <div class="flex-1 bg-surface-container-low h-2 rounded-full overflow-hidden">
                            <div class="{{ $pc }} h-full rounded-full" style="width: {{ $total > 0 ? round($pv / $total * 100) : 0 }}%;"></div>
                        </div>
                        <span class="w-8 text-right font-semibold text-on-surface">{{ $pv }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        {{-- Workload --}}
        <div class="xl:col-span-4 bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
            <div class="flex items-center gap-2">
                <x-rune-icon name="metrics-activity" class="text-secondary w-5 h-5" />
                <h3 class="font-headline-md text-headline-md text-on-surface">Team Workload</h3>
            </div>
            @forelse ($workload as $row)
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between font-body-sm text-body-sm">
                        <span class="font-medium text-on-surface truncate">{{ $row->name }}</span>
                        <span class="font-bold {{ $row->assigned_tasks_count >= 7 ? 'text-error' : 'text-on-surface' }}">{{ $row->assigned_tasks_count }} task</span>
                    </div>
                    <div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                        <div class="{{ $row->assigned_tasks_count >= 7 ? 'bg-error' : 'bg-secondary' }} h-full rounded-full" style="width: {{ min(100, (int) round($row->assigned_tasks_count / 8 * 100)) }}%;"></div>
                    </div>
                </div>
            @empty
                <p class="font-body-sm text-body-sm text-outline">No active workload.</p>
            @endforelse
        </div>

        {{-- Summary --}}
        <div class="xl:col-span-8 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-space-md gap-2">
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface">Project Recap &amp; SLA Compliance</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">SLA = % of tasks completed on time (completed ≤ due date).</p>
                </div>
                <div class="relative print:hidden">
                    <x-rune-icon name="tools-list-filter" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-4 h-4" />
                    <input wire:model.live.debounce.300ms="q" type="text" placeholder="Search projects..." class="h-8 pl-8 pr-3 bg-surface-container-low rounded-lg font-body-sm text-body-sm outline-none focus:bg-surface-container transition-all" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left font-body-default text-body-default min-w-[640px]">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant font-label-uppercase text-label-uppercase">
                            <th class="py-space-sm px-space-md rounded-l-lg">Project Name</th>
                            <th class="py-space-sm px-space-md">Task Volume</th>
                            <th class="py-space-sm px-space-md w-1/4">Actual Progress</th>
                            <th class="py-space-sm px-space-md">SLA Rate</th>
                            <th class="py-space-sm px-space-md rounded-r-lg text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="py-space-sm px-space-md">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-primary-container text-on-primary flex items-center justify-center shrink-0">
                                            <x-rune-icon name="tools-house" class="w-5 h-5" />
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-title-sm text-title-sm text-on-surface font-semibold truncate">{{ $project->name }}</span>
                                            <span class="font-label-default text-label-default text-outline">{{ $project->creator?->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-space-sm px-space-md whitespace-nowrap font-semibold text-on-surface">{{ $project->tasks_count }} <span class="font-normal text-on-surface-variant">({{ $project->done_tasks_count }} done)</span></td>
                                <td class="py-space-sm px-space-md">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-surface-container h-2 rounded-full overflow-hidden">
                                            <div class="bg-secondary h-full rounded-full" style="width: {{ $project->progress }}%;"></div>
                                        </div>
                                        <span class="font-semibold text-on-surface text-body-sm">{{ $project->progress }}%</span>
                                    </div>
                                </td>
                                <td class="py-space-sm px-space-md whitespace-nowrap">
                                    @if ($sla[$project->id] === null)
                                        <span class="text-outline font-body-sm text-body-sm">—</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full font-label-default text-label-default font-semibold {{ $sla[$project->id] >= 80 ? 'bg-tertiary-fixed/40 text-on-tertiary-fixed-variant' : ($sla[$project->id] >= 50 ? 'bg-secondary-fixed text-on-secondary-fixed-variant' : 'bg-error-container text-on-error-container') }}">{{ $sla[$project->id] }}%</span>
                                    @endif
                                </td>
                                <td class="py-space-sm px-space-md text-right whitespace-nowrap">
                                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 text-secondary font-body-sm text-body-sm font-semibold hover:underline">
                                        Open <x-rune-icon name="arrows-chevron-right" class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center font-body-sm text-body-sm text-on-surface-variant">No projects for this filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
