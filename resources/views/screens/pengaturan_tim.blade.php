@php
$roleOrder = ['admin', 'project-manager', 'team-member', 'viewer'];
$rolesByName = $roles->keyBy('name');
$tints = ['bg-secondary-fixed text-on-secondary-fixed', 'bg-primary-fixed text-primary-container', 'bg-surface-container-high text-on-surface', 'bg-secondary-fixed-dim text-on-secondary-fixed-variant'];
$statusTone = ['ok' => 'text-on-tertiary-container', 'warning' => 'text-error', 'error' => 'text-error'];
@endphp

<div class="flex flex-col w-full">
    <!-- Top Command Context Bar -->
    <div class="px-space-xl pt-space-xl pb-space-lg flex flex-col gap-space-lg bg-surface-container-lowest shadow-sm">
        <!-- Breadcrumb & Status Marker -->
        <div class="flex flex-wrap items-center justify-between gap-space-sm">
            <div class="flex items-center gap-space-xs font-label-uppercase text-label-uppercase text-outline tracking-wider">
                <span>ADMINISTRATION</span>
                <x-heroicon-o-chevron-right class="w-[13px] h-[13px] text-outline" />
                <span>SYSTEM ACCESS RIGHTS</span>
                <x-heroicon-o-chevron-right class="w-[13px] h-[13px] text-outline" />
                <span class="text-secondary font-semibold">ROLE-BASED ACCESS CONTROL (RBAC)</span>
            </div>
            <div class="inline-flex items-center gap-space-xs px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-default text-label-default">
                <span class="w-2 h-2 rounded-full bg-on-tertiary-container animate-pulse"></span>
                <span>INFORMATION SYSTEMS BUREAU • CAMPUS ICT DIRECTORATE</span>
            </div>
        </div>
        <!-- Title and Action Hub -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
            <div class="flex flex-col max-w-3xl">
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">
                    Team Management &amp; Access Rights Settings (RBAC)
                </h1>
                <p class="font-body-default text-body-default text-on-surface-variant mt-1">
                    Manage roles, access permissions for faculty &amp; bureau work units, Google Workspace SSO synchronization <span class="font-semibold text-on-surface">@@upnyk.ac.id</span>, and digital security compliance audit of BSI.
                </p>
            </div>
            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-space-sm self-start lg:self-auto shrink-0">
                @if ($canManage)
                <a href="{{ route('team.audit.export') }}" class="inline-flex items-center gap-space-xs h-9 px-3.5 rounded-xl bg-surface-container-low text-on-surface hover:bg-surface-container text-body-medium font-body-medium transition-colors shadow-sm cursor-pointer" type="button">
                    <x-heroicon-o-arrow-down-tray class="w-[18px] h-[18px] text-on-surface-variant" />
                    <span>Export Access Log</span>
                </a>
                <button wire:click="openMatrix" class="inline-flex items-center gap-space-xs h-9 px-3.5 rounded-xl bg-surface-container-low text-on-surface hover:bg-surface-container text-body-medium font-body-medium transition-colors shadow-sm cursor-pointer" type="button">
                    <x-heroicon-o-shield-check class="w-[18px] h-[18px] text-on-surface-variant" />
                    <span>Permission Matrix</span>
                </button>
                @endif
                @if ($canInvite)
                <button wire:click="openAddMember" class="inline-flex items-center gap-space-xs h-9 px-4 rounded-xl bg-primary-container text-on-primary hover:bg-primary text-body-medium font-body-medium transition-colors shadow-md shadow-primary-container/20 cursor-pointer" type="button">
                    <x-heroicon-o-user-plus class="w-[18px] h-[18px]" />
                    <span>Add Team Member</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    <!-- Main Canvas Container -->
    <div class="p-space-xl flex flex-col gap-space-xl">
        <!-- Institutional Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
            <!-- Metric 1 -->
            <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Total Active Personnel</span>
                        <div class="flex items-baseline gap-space-xs mt-1">
                            <span class="font-headline-xl text-headline-xl font-bold text-on-surface">{{ $metrics['total'] }}</span>
                            <span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Accounts</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary">
                        <x-heroicon-o-identification class="w-[22px] h-[22px]" />
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
                    <span class="truncate">BSI Core &amp; Faculty PIC</span>
                    <span class="font-label-default text-label-default text-secondary font-semibold">Active</span>
                </div>
            </div>
            <!-- Metric 2 -->
            <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Campus SSO Integration</span>
                        <div class="flex items-baseline gap-space-xs mt-1">
                            <span class="font-headline-xl text-headline-xl font-bold text-tertiary-container">{{ $metrics['ssoPercent'] }}%</span>
                            <span class="font-body-sm text-body-sm text-on-tertiary-container font-semibold">2FA Enrolled</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-on-tertiary-container">
                        <x-heroicon-s-shield-check class="w-[22px] h-[22px]" />
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center gap-space-xs text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
                    <x-heroicon-o-arrow-path class="w-[16px] h-[16px] text-on-tertiary-container" />
                    <span class="truncate">Enforce 2FA Google Workspace</span>
                </div>
            </div>
            <!-- Metric 3 -->
            <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Superadmin Role</span>
                        <div class="flex items-baseline gap-space-xs mt-1">
                            <span class="font-headline-xl text-headline-xl font-bold text-primary-container">{{ $metrics['superadmins'] }}</span>
                            <span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Supervisors</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-container">
                        <x-heroicon-o-cog-6-tooth class="w-[22px] h-[22px]" />
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
                    <span class="truncate">Admin role holders</span>
                    <span class="px-1.5 py-0.5 rounded bg-surface-variant text-on-primary-fixed-variant font-code-sm text-code-sm font-semibold">Level 0</span>
                </div>
            </div>
            <!-- Metric 4 -->
            <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Pending Review</span>
                        <div class="flex items-baseline gap-space-xs mt-1">
                            <span class="font-headline-xl text-headline-xl font-bold text-error">{{ $metrics['pending'] }}</span>
                            <span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Requests</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-error-container flex items-center justify-center text-on-error-container">
                        <x-heroicon-o-clock class="w-[22px] h-[22px]" />
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-error-container bg-error-container/40 px-2.5 py-1.5 rounded-lg">
                    <span class="truncate">Accounts awaiting 2FA</span>
                    @if ($canManage && $metrics['pending'] > 0)
                    <button wire:click="reviewPending" class="font-label-default text-label-default font-semibold underline underline-offset-2 hover:opacity-80 cursor-pointer" type="button">Review</button>
                    @endif
                </div>
            </div>
        </div>
        <!-- Main Table Container -->
        <div class="rounded-xl bg-surface-container-lowest shadow-sm flex flex-col overflow-hidden">
            <!-- Tabs Navigation -->
            <div class="flex flex-wrap items-center justify-between px-space-lg pt-space-sm bg-surface-container-low gap-space-sm">
                <div class="flex items-center gap-space-xs overflow-x-auto">
                    <button wire:click="setTab('members')" class="flex items-center gap-space-sm px-space-md py-3 font-body-medium text-body-medium transition-colors cursor-pointer {{ $tab === 'members' ? 'text-secondary font-semibold relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-secondary' : 'text-on-surface-variant hover:text-on-surface' }}" type="button">
                        <x-heroicon-o-user-group class="w-[18px] h-[18px]" />
                        <span>Team Member List</span>
                        <span class="px-2 py-0.5 rounded-full {{ $tab === 'members' ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant' }} font-code-sm text-code-sm">{{ $metrics['total'] }}</span>
                    </button>
                    <button wire:click="setTab('roles')" class="flex items-center gap-space-sm px-space-md py-3 font-body-medium text-body-medium transition-colors cursor-pointer {{ $tab === 'roles' ? 'text-secondary font-semibold relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-secondary' : 'text-on-surface-variant hover:text-on-surface' }}" type="button">
                        <x-heroicon-o-document-check class="w-[18px] h-[18px]" />
                        <span>Role &amp; Access Rights Structure</span>
                    </button>
                    <button wire:click="setTab('units')" class="flex items-center gap-space-sm px-space-md py-3 font-body-medium text-body-medium transition-colors cursor-pointer {{ $tab === 'units' ? 'text-secondary font-semibold relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-secondary' : 'text-on-surface-variant hover:text-on-surface' }}" type="button">
                        <x-heroicon-o-building-office-2 class="w-[18px] h-[18px]" />
                        <span>Work Unit &amp; Faculty Groups</span>
                        <span class="px-1.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-code-sm text-code-sm">{{ $units->count() }} Units</span>
                    </button>
                    @if ($canManage)
                    <button wire:click="setTab('audit')" class="flex items-center gap-space-sm px-space-md py-3 font-body-medium text-body-medium transition-colors cursor-pointer {{ $tab === 'audit' ? 'text-secondary font-semibold relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-secondary' : 'text-on-surface-variant hover:text-on-surface' }}" type="button">
                        <x-heroicon-o-clock class="w-[18px] h-[18px]" />
                        <span>Security Audit Log</span>
                    </button>
                    @endif
                </div>
                <div class="flex items-center gap-space-xs pb-2 lg:pb-0">
                    <span class="font-code-sm text-code-sm text-outline">Last Sync: {{ $lastSync ? \Illuminate\Support\Carbon::parse($lastSync)->format('H:i') . ' WIB' : '-' }}</span>
                    <button wire:click="$refresh" class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition-colors cursor-pointer" type="button">
                        <x-heroicon-o-arrow-path class="w-[18px] h-[18px]" />
                    </button>
                </div>
            </div>

            @if ($tab === 'members')
            <!-- Action & Filter Bar -->
            <div class="p-space-lg flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-space-md bg-surface-container-lowest">
                <!-- Search Field -->
                <div class="relative flex-1 max-w-xl">
                    <x-heroicon-o-magnifying-glass class="w-[18px] h-[18px] absolute left-3 top-1/2 -translate-y-1/2 text-outline" />
                    <input wire:model.live.debounce.300ms="q" class="w-full h-9 pl-9 pr-space-md bg-surface-container-low text-on-surface rounded-xl font-body-sm text-body-sm outline-none focus:bg-surface-container transition-all" placeholder="Search name, NIP/NIDN, institutional email @@upnyk.ac.id, or expertise..." type="text" />
                </div>
                <!-- Filter Dropdowns & View Mode -->
                <div class="flex flex-wrap items-center gap-space-sm">
                    <div class="relative">
                        <select wire:model.live="role" class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
                            <option value="all">All Roles (RBAC)</option>
                            @foreach ($roleOptions as $option)
                            <option value="{{ $option->name }}">{{ $roleMeta[$option->name]['label'] ?? $option->name }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-[16px] h-[16px] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline" />
                    </div>
                    <div class="relative">
                        <select wire:model.live="unit" class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
                            <option value="all">All Units ({{ $units->count() }} Units)</option>
                            @foreach ($unitOptions as $option)
                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-[16px] h-[16px] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline" />
                    </div>
                    <div class="relative">
                        <select wire:model.live="status" class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
                            <option value="all">Status: All</option>
                            <option value="active">Status: Active</option>
                            <option value="pending_2fa">Status: Pending 2FA</option>
                            <option value="suspended">Status: Suspended</option>
                        </select>
                        <x-heroicon-o-chevron-down class="w-[16px] h-[16px] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline" />
                    </div>
                    <div class="flex items-center bg-surface-container-low p-0.5 rounded-xl">
                        <button class="p-1.5 rounded-lg bg-surface-container-lowest text-secondary shadow-sm cursor-pointer" type="button">
                            <x-heroicon-o-table-cells class="w-[18px] h-[18px]" />
                        </button>
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface transition-colors cursor-pointer" type="button">
                            <x-heroicon-o-squares-2x2 class="w-[18px] h-[18px]" />
                        </button>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low font-label-uppercase text-label-uppercase text-outline tracking-wider uppercase select-none">
                            <th class="py-3 px-space-lg w-12 text-center">
                                <input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox" />
                            </th>
                            <th class="py-3 px-space-md">Team Members &amp; NIP/NIDN</th>
                            <th class="py-3 px-space-md">Work Unit &amp; Location</th>
                            <th class="py-3 px-space-md">System Role (RBAC)</th>
                            <th class="py-3 px-space-md">Project Scope Access</th>
                            <th class="py-3 px-space-md">Security Compliance</th>
                            <th class="py-3 px-space-lg text-right">Management Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0 text-body-sm font-body-sm">
                        @forelse ($members as $member)
                        @php
                        $meta = $member->roleMeta();
                        $security = $member->securityStatus();
                        $tint = $tints[$member->id % 4];
                        @endphp
                        <tr wire:key="member-{{ $member->id }}" class="hover:bg-surface-container-low transition-colors {{ $loop->even ? 'bg-surface-container-low/30' : 'bg-surface-container-lowest' }}">
                            <td class="py-3 px-space-lg text-center">
                                <input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox" />
                            </td>
                            <td class="py-3 px-space-md">
                                <div class="flex items-center gap-space-sm min-w-[240px]">
                                    @if ($member->avatar_url)
                                    <img alt="{{ $member->name }}" class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-secondary/20" src="{{ $member->avatar_url }}" />
                                    @else
                                    <div class="w-9 h-9 rounded-full {{ $tint }} flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">{{ $member->initials() }}</div>
                                    @endif
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">{{ $member->name }}</span>
                                        <span class="font-code-sm text-code-sm text-on-surface-variant font-medium">{{ $member->nip ? 'NIP. '.$member->nip : 'No NIP' }}</span>
                                        <span class="font-code-sm text-code-sm text-secondary truncate">{{ $member->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-space-md whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="font-body-medium text-body-medium text-on-surface font-medium">{{ $member->workUnit?->name ?? 'Unassigned' }}</span>
                                    <div class="flex items-center gap-1 text-on-surface-variant">
                                        <x-heroicon-o-map-pin class="w-3 h-3 text-outline" />
                                        <span class="font-code-sm text-code-sm">{{ $member->campus ?? ($member->workUnit?->campus ?? '-') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-space-md">
                                <div class="flex flex-col items-start gap-1">
                                    <span class="px-2.5 py-0.5 rounded-full {{ $meta['badge'] }} font-label-default text-label-default font-semibold inline-flex items-center gap-1">
                                        <!-- <x-dynamic-component :component="'heroicon-o-'.$meta['icon']" class="w-[12px] h-[12px]" /> -->
                                        <span>{{ $meta['label'] }}</span>
                                    </span>
                                    <span class="font-code-sm text-code-sm text-on-surface-variant">{{ $member->position ?: 'Level '.$meta['level'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-space-md">
                                <div class="flex flex-col gap-1 min-w-[200px]">
                                    <span class="font-body-medium text-body-medium text-on-surface font-semibold">{{ $member->projects->count() }} Active Projects</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($member->projects->take(2) as $project)
                                        <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">{{ $project->name }}</span>
                                        @endforeach
                                        @if ($member->projects->count() > 2)
                                        <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface font-code-sm text-code-sm">+{{ $member->projects->count() - 2 }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-space-md whitespace-nowrap">
                                <div class="flex flex-col">
                                    <div class="inline-flex items-center gap-1 {{ $statusTone[$security['tone']] }} font-label-default text-label-default font-semibold">
                                        <x-heroicon-o-shield-check class="w-[14px] h-[14px]" />
                                        <span>{{ $security['label'] }}</span>
                                    </div>
                                    <span class="font-code-sm text-code-sm text-outline">Login: {{ $member->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-space-lg text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-space-xs">
                                    @if ($canManage && $security['tone'] !== 'ok')
                                    <button wire:click="approve({{ $member->id }})" class="px-3 py-1.5 rounded-xl bg-secondary text-on-secondary hover:bg-secondary-container font-body-medium text-body-medium font-semibold transition-colors cursor-pointer" type="button">
                                        Approve
                                    </button>
                                    @endif
                                    @if ($canManage)
                                    <button wire:click="openManageMember({{ $member->id }})" class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors cursor-pointer" type="button">
                                        Manage Permissions
                                    </button>
                                    @endif
                                    @if ($canManage)
                                    <div x-data="{ open: false }" class="relative">
                                        <button x-on:click="open = ! open" class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors cursor-pointer" type="button">
                                            <x-heroicon-o-ellipsis-vertical class="w-[18px] h-[18px]" />
                                        </button>
                                        <div x-show="open" x-cloak x-on:click.outside="open = false" class="absolute right-0 mt-1 w-44 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/40 overflow-hidden z-30 text-left">
                                            @if ($member->isSuspended())
                                            <button wire:click="reactivate({{ $member->id }})" x-on:click="open = false" class="w-full text-left px-space-md py-2 font-body-sm text-body-sm text-secondary hover:bg-surface-container-low cursor-pointer" type="button">Reactivate access</button>
                                            @else
                                            <button wire:click="suspend({{ $member->id }})" wire:confirm="Suspend {{ $member->name }}?" x-on:click="open = false" class="w-full text-left px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container/30 cursor-pointer" type="button">Suspend access</button>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-space-xl text-center">
                                <p class="font-body-medium text-body-medium text-on-surface font-semibold">No team members found</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Adjust the search or filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination & Selection Footer -->
            <div class="px-space-lg py-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-md">
                <div class="flex items-center gap-space-sm font-body-sm text-body-sm text-on-surface-variant">
                    <span>Showing <span class="font-semibold text-on-surface">{{ $members->firstItem() ?? 0 }} - {{ $members->lastItem() ?? 0 }}</span> of <span class="font-semibold text-on-surface">{{ $members->total() }}</span> registered personnel</span>
                    <span class="text-outline">|</span>
                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm font-medium">{{ $perPage }} rows per page</span>
                </div>
                <div>{{ $members->links() }}</div>
            </div>
            @endif

            @if ($tab === 'roles')
            <div class="p-space-lg flex flex-col gap-space-md">
                @foreach ($roles->sortBy(fn ($r) => array_search($r->name, $roleOrder, true) ?: 99) as $roleRow)
                @php
                $rowMeta = $roleMeta[$roleRow->name] ?? ['label' => $roleRow->name, 'level' => '-', 'badge' => 'bg-surface-variant text-on-surface-variant', 'icon' => 'user'];
                @endphp
                <div wire:key="role-{{ $roleRow->id }}" class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/30 flex flex-col gap-space-sm">
                    <div class="flex items-start justify-between gap-space-sm">
                        <div class="flex items-center gap-space-sm">
                            <span class="px-2.5 py-0.5 rounded-full {{ $rowMeta['badge'] }} font-label-default text-label-default font-semibold inline-flex items-center gap-1">
                                <x-dynamic-component :component="'heroicon-o-'.$rowMeta['icon']" class="w-[12px] h-[12px]" />
                                <span>{{ $rowMeta['label'] }}</span>
                            </span>
                            <span class="font-code-sm text-code-sm text-outline">{{ $roleRow->name }} • Level {{ $rowMeta['level'] }}</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $roleRow->users_count }} member(s) • {{ $roleRow->permissions->count() }} permissions</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($roleRow->permissions->pluck('name')->sort() as $permissionName)
                        <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">{{ $permissionName }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if ($tab === 'units')
            <div class="p-space-lg grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-space-md">
                @forelse ($units as $workUnit)
                <div wire:key="unit-{{ $workUnit->id }}" class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/30 flex flex-col gap-space-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-space-sm">
                            <div class="w-9 h-9 rounded-xl bg-primary-fixed text-primary-container flex items-center justify-center">
                                <x-heroicon-o-building-office-2 class="w-[20px] h-[20px]" />
                            </div>
                            <div class="flex flex-col">
                                <span class="font-body-medium text-body-medium font-semibold text-on-surface">{{ $workUnit->name }}</span>
                                <span class="font-code-sm text-code-sm text-outline">{{ $workUnit->code }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-code-sm text-code-sm">{{ $workUnit->users_count }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-on-surface-variant">
                        <x-heroicon-o-map-pin class="w-[13px] h-[13px] text-outline" />
                        <span class="font-code-sm text-code-sm">{{ $workUnit->campus ?? '-' }}</span>
                    </div>
                    <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">{{ $workUnit->type }}</span>
                </div>
                @empty
                <div class="col-span-full text-center py-space-xl">
                    <p class="font-body-medium text-body-medium text-on-surface font-semibold">No work units</p>
                </div>
                @endforelse
            </div>
            @endif

            @if ($tab === 'audit')
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low font-label-uppercase text-label-uppercase text-outline tracking-wider uppercase select-none">
                            <th class="py-3 px-space-lg">Timestamp</th>
                            <th class="py-3 px-space-md">Subject</th>
                            <th class="py-3 px-space-md">Actor</th>
                            <th class="py-3 px-space-md">Event</th>
                            <th class="py-3 px-space-md">Description</th>
                            <th class="py-3 px-space-md">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0 text-body-sm font-body-sm">
                        @forelse ($auditLogs as $log)
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-surface-container-low transition-colors {{ $loop->even ? 'bg-surface-container-low/30' : 'bg-surface-container-lowest' }}">
                            <td class="py-3 px-space-lg whitespace-nowrap font-code-sm text-code-sm text-on-surface-variant">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-space-md whitespace-nowrap font-medium text-on-surface">{{ $log->user?->name ?? '-' }}</td>
                            <td class="py-3 px-space-md whitespace-nowrap text-on-surface-variant">{{ $log->actor?->name ?? '-' }}</td>
                            <td class="py-3 px-space-md whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">{{ $log->event }}</span>
                            </td>
                            <td class="py-3 px-space-md text-on-surface-variant">{{ $log->description ?? '-' }}</td>
                            <td class="py-3 px-space-md whitespace-nowrap font-code-sm text-code-sm text-outline">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-space-xl text-center">
                                <p class="font-body-medium text-body-medium text-on-surface font-semibold">No audit entries yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between gap-space-md">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Showing <span class="font-semibold text-on-surface">{{ $auditLogs->firstItem() ?? 0 }} - {{ $auditLogs->lastItem() ?? 0 }}</span> of <span class="font-semibold text-on-surface">{{ $auditLogs->total() }}</span> entries</span>
                <div>{{ $auditLogs->links() }}</div>
            </div>
            @endif
        </div>
        <!-- Asymmetric Bottom Grid: RBAC Quick Reference & SSO Security Policy -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <!-- RBAC Quick Matrix Overview (7 Columns) -->
            <div class="lg:col-span-7 p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-space-md">
                        <div class="flex items-center gap-space-sm">
                            <x-heroicon-o-view-columns class="w-[22px] h-[22px] text-secondary" />
                            <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Quick Role Permission Matrix</h2>
                        </div>
                        @if ($canManage)
                        <button wire:click="openMatrix" class="font-label-default text-label-default text-secondary hover:underline font-semibold flex items-center gap-0.5 cursor-pointer" type="button">
                            <span>Open Full Configuration</span>
                            <x-heroicon-o-arrow-right class="w-[14px] h-[14px]" />
                        </button>
                        @endif
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
                        Mapping of data operation rights (CRUD) according to campus task hierarchy and BSI audit compliance of UPN "Veteran" Yogyakarta.
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body-sm text-body-sm">
                            <thead>
                                <tr class="bg-surface-container-low font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">
                                    <th class="py-2.5 px-3">Role Level</th>
                                    @foreach ($matrixColumns as $column)
                                    <th class="py-2.5 px-3 text-center">{{ $column['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y-0 font-code-sm text-code-sm">
                                @foreach ($roleOrder as $roleName)
                                @php
                                $matrixRole = $rolesByName->get($roleName);
                                $matrixMeta = $roleMeta[$roleName] ?? ['label' => $roleName];
                                @endphp
                                <tr wire:key="matrix-{{ $roleName }}" class="hover:bg-surface-container-low/50 {{ $loop->even ? 'bg-surface-container-low/20' : '' }}">
                                    <td class="py-2.5 px-3 font-semibold text-on-surface flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $roleName === 'admin' ? 'bg-primary-container' : ($roleName === 'project-manager' ? 'bg-secondary' : ($roleName === 'team-member' ? 'bg-secondary-fixed-dim' : 'bg-surface-container-highest')) }}"></span>
                                        <span>{{ $matrixMeta['label'] }}</span>
                                    </td>
                                    @foreach ($matrixColumns as $column)
                                    @php $allowed = $matrixRole?->permissions->contains('name', $column['permission']) ?? false; @endphp
                                    <td class="py-2.5 px-3 text-center {{ $allowed ? 'text-on-tertiary-container' : 'text-outline' }}">
                                        @if ($allowed)
                                        <x-heroicon-o-check class="w-[18px] h-[18px]" />
                                        @else
                                        <x-heroicon-o-minus class="w-[18px] h-[18px]" />
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-3 py-2 rounded-lg">
                    <span class="flex items-center gap-1 font-label-default text-label-default">
                        <x-heroicon-o-information-circle class="w-[16px] h-[16px] text-secondary" />
                        <span>Permission changes require multi-factor authorization from the BSI Cybersecurity Coordinator.</span>
                    </span>
                    @if ($canManage)
                    <button wire:click="setTab('audit')" class="text-secondary font-semibold hover:underline font-label-default text-label-default cursor-pointer" type="button">Permission Log</button>
                    @endif
                </div>
            </div>
            <!-- Security Policies & SSO Enforcement Card (5 Columns) -->
            <div class="lg:col-span-5 p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-space-md">
                        <div class="flex items-center gap-space-sm">
                            <x-heroicon-o-lock-closed class="w-[22px] h-[22px] text-primary-container" />
                            <h2 class="font-headline-md text-headline-md text-on-surface font-bold">SSO Security Policy</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-surface-container text-on-tertiary-container font-code-sm text-code-sm font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>
                            <span>Mandatory</span>
                        </span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
                        Centralized authentication protocol that applies automatically to all personnel registered in the UPNYK campus domain.
                    </p>
                    <div class="flex flex-col gap-space-sm">
                        <!-- Policy Item 1 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex items-start justify-between gap-space-sm">
                            <div class="flex items-start gap-space-sm">
                                <x-heroicon-o-key class="w-[20px] h-[20px] text-secondary mt-0.5" />
                                <div class="flex flex-col">
                                    <span class="font-body-medium text-body-medium font-semibold text-on-surface">Enforced Two-Factor Authentication (2FA)</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Mandatory authentication via Google Authenticator or FIDO2 Key.</span>
                                </div>
                            </div>
                            <span class="text-on-tertiary-container font-label-default text-label-default font-semibold shrink-0">{{ $metrics['ssoPercent'] }}%</span>
                        </div>
                        <!-- Policy Item 2 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex items-start justify-between gap-space-sm">
                            <div class="flex items-start gap-space-sm">
                                <x-heroicon-o-clock class="w-[20px] h-[20px] text-secondary mt-0.5" />
                                <div class="flex flex-col">
                                    <span class="font-body-medium text-body-medium font-semibold text-on-surface">Active Session Duration (Session Timeout)</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Session is automatically terminated if there is no activity for a full 4 hours.</span>
                                </div>
                            </div>
                            <span class="font-code-sm text-code-sm font-bold text-on-surface shrink-0">4 Hours</span>
                        </div>
                        <!-- Policy Item 3 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex items-start justify-between gap-space-sm">
                            <div class="flex items-start gap-space-sm">
                                <x-heroicon-o-globe-alt class="w-[20px] h-[20px] text-secondary mt-0.5" />
                                <div class="flex flex-col">
                                    <span class="font-body-medium text-body-medium font-semibold text-on-surface">Staging &amp; Database IP Restriction</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Restricted to the Babarsari Campus local subnet (10.10.x.x) or UPN WireGuard VPN.</span>
                                </div>
                            </div>
                            <span class="text-on-tertiary-container font-label-default text-label-default font-semibold shrink-0">Isolated</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-space-xs text-outline font-code-sm text-code-sm">
                        <x-heroicon-s-check-badge class="w-[15px] h-[15px]" />
                        <span>ISO/IEC 27001 Audit Compliant</span>
                    </div>
                    @if ($canManage)
                    <button wire:click="openMatrix" class="font-label-default text-label-default text-secondary hover:underline font-semibold cursor-pointer" type="button">
                        Advanced Security Configuration
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($canInvite)
    <livewire:add-team-member />
    @endif

    @if ($canManage)
    <livewire:manage-member />
    <livewire:permission-matrix />
    @endif
</div>
