@extends('layouts.app')

@section('title', 'Team Settings')
@section('breadcrumb', 'Team Settings')

@section('content')
<div class="flex flex-col w-full">
<!-- Top Command Context Bar -->
<div class="px-space-xl pt-space-xl pb-space-lg flex flex-col gap-space-lg bg-surface-container-lowest shadow-sm">
<!-- Breadcrumb & Status Marker -->
<div class="flex flex-wrap items-center justify-between gap-space-sm">
<div class="flex items-center gap-space-xs font-label-uppercase text-label-uppercase text-outline tracking-wider">
<span>ADMINISTRATION</span>
<span class="material-symbols-outlined text-[13px] text-outline">chevron_right</span>
<span>SYSTEM ACCESS RIGHTS</span>
<span class="material-symbols-outlined text-[13px] text-outline">chevron_right</span>
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
<button class="inline-flex items-center gap-space-xs h-9 px-3.5 rounded-xl bg-surface-container-low text-on-surface hover:bg-surface-container text-body-medium font-body-medium transition-colors shadow-sm" type="button">
<span class="material-symbols-outlined text-[18px] text-on-surface-variant">download</span>
<span>Export Access Log</span>
</button>
<button class="inline-flex items-center gap-space-xs h-9 px-3.5 rounded-xl bg-surface-container-low text-on-surface hover:bg-surface-container text-body-medium font-body-medium transition-colors shadow-sm" type="button">
<span class="material-symbols-outlined text-[18px] text-on-surface-variant">security</span>
<span>Permission Matrix</span>
</button>
<button class="inline-flex items-center gap-space-xs h-9 px-4 rounded-xl bg-primary-container text-on-primary hover:bg-primary text-body-medium font-body-medium transition-colors shadow-md shadow-primary-container/20" type="button">
<span class="material-symbols-outlined text-[18px]">person_add</span>
<span>Add Team Member</span>
</button>
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
<span class="font-headline-xl text-headline-xl font-bold text-on-surface">28</span>
<span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Accounts</span>
</div>
</div>
<div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-[22px]">badge</span>
</div>
</div>
<div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
<span class="truncate">6 BSI Core • 22 Faculty PIC</span>
<span class="font-label-default text-label-default text-secondary font-semibold">100% Active</span>
</div>
</div>
<!-- Metric 2 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Campus SSO Integration</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-tertiary-container">100%</span>
<span class="font-body-sm text-body-sm text-on-tertiary-container font-semibold">Integrated</span>
</div>
</div>
<div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-[22px]">verified_user</span>
</div>
</div>
<div class="mt-4 pt-3 flex items-center gap-space-xs text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
<span class="material-symbols-outlined text-[16px] text-on-tertiary-container">lock_reset</span>
<span class="truncate">Enforce 2FA Google Workspace</span>
</div>
</div>
<!-- Metric 3 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Superadmin Role</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-primary-container">3</span>
<span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Supervisors</span>
</div>
</div>
<div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[22px]">admin_panel_settings</span>
</div>
</div>
<div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1.5 rounded-lg">
<span class="truncate">Achmad Fauzi + 2 SecOps</span>
<span class="px-1.5 py-0.5 rounded bg-surface-variant text-on-primary-fixed-variant font-code-sm text-code-sm font-semibold">Level 0</span>
</div>
</div>
<!-- Metric 4 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Pending Review</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-error">2</span>
<span class="font-title-sm text-title-sm text-on-surface-variant font-medium">Requests</span>
</div>
</div>
<div class="w-10 h-10 rounded-xl bg-error-container flex items-center justify-center text-on-error-container">
<span class="material-symbols-outlined text-[22px]">pending_actions</span>
</div>
</div>
<div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-error-container bg-error-container/40 px-2.5 py-1.5 rounded-lg">
<span class="truncate">PIC FTI &amp; SI Study Program</span>
<button class="font-label-default text-label-default font-semibold underline underline-offset-2 hover:opacity-80" type="button">Review</button>
</div>
</div>
</div>
<!-- Main Table Container -->
<div class="rounded-xl bg-surface-container-lowest shadow-sm flex flex-col overflow-hidden">
<!-- Tabs Navigation -->
<div class="flex flex-wrap items-center justify-between px-space-lg pt-space-sm bg-surface-container-low gap-space-sm">
<div class="flex items-center gap-space-xs overflow-x-auto">
<button class="flex items-center gap-space-sm px-space-md py-3 text-secondary font-body-medium text-body-medium font-semibold relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-secondary" type="button">
<span class="material-symbols-outlined text-[18px]">group</span>
<span>Team Member List</span>
<span class="px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-code-sm text-code-sm">28</span>
</button>
<button class="flex items-center gap-space-sm px-space-md py-3 text-on-surface-variant hover:text-on-surface font-body-medium text-body-medium transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">policy</span>
<span>Role &amp; Access Rights Structure</span>
</button>
<button class="flex items-center gap-space-sm px-space-md py-3 text-on-surface-variant hover:text-on-surface font-body-medium text-body-medium transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">domain</span>
<span>Work Unit &amp; Faculty Groups</span>
<span class="px-1.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-code-sm text-code-sm">8 Units</span>
</button>
<button class="flex items-center gap-space-sm px-space-md py-3 text-on-surface-variant hover:text-on-surface font-body-medium text-body-medium transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">history</span>
<span>Security Audit Log</span>
</button>
</div>
<div class="flex items-center gap-space-xs pb-2 lg:pb-0">
<span class="font-code-sm text-code-sm text-outline">Last Sync: 10:42 WIB</span>
<button class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">refresh</span>
</button>
</div>
</div>
<!-- Action & Filter Bar -->
<div class="p-space-lg flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-space-md bg-surface-container-lowest">
<!-- Search Field -->
<div class="relative flex-1 max-w-xl">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
<input class="w-full h-9 pl-9 pr-space-md bg-surface-container-low text-on-surface rounded-xl font-body-sm text-body-sm outline-none focus:bg-surface-container transition-all" placeholder="Search name, NIP/NIDN, institutional email @@upnyk.ac.id, or expertise..." type="text"/>
</div>
<!-- Filter Dropdowns & View Mode -->
<div class="flex flex-wrap items-center gap-space-sm">
<div class="relative">
<select class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
<option>All Roles (RBAC)</option>
<option>Superadmin</option>
<option>Lead Developer</option>
<option>DevOps &amp; Infra</option>
<option>Frontend / UI/UX</option>
<option>QA Specialist</option>
<option>Faculty Unit PIC</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline text-[16px]">keyboard_arrow_down</span>
</div>
<div class="relative">
<select class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
<option>All Units (8 Units)</option>
<option>BSI Babarsari</option>
<option>BSI Condongcatur</option>
<option>FTI (Industrial Engineering)</option>
<option>FEB (Economics &amp; Business)</option>
<option>FISIP</option>
<option>Rectorate &amp; LPPM</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline text-[16px]">keyboard_arrow_down</span>
</div>
<div class="relative">
<select class="appearance-none h-9 pl-3 pr-8 bg-surface-container-low text-on-surface font-body-sm text-body-sm rounded-xl outline-none cursor-pointer hover:bg-surface-container transition-colors">
<option>Status: Active</option>
<option>Status: Pending 2FA</option>
<option>Status: Suspended</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline text-[16px]">keyboard_arrow_down</span>
</div>
<div class="flex items-center bg-surface-container-low p-0.5 rounded-xl">
<button class="p-1.5 rounded-lg bg-surface-container-lowest text-secondary shadow-sm" type="button">
<span class="material-symbols-outlined text-[18px]">table_rows</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">grid_view</span>
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
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
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
<!-- Row 1: Achmad Fauzi -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-lowest">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<img alt="Achmad Fauzi" class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-secondary/20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDapX-yjCkrV5LUvAGjThYgCw1Lk0J-myysDmiXhxQ6tHlsJPHeOHMFnxXCcgza3cb3mw6dvbL1eijGV5QRNjZb2j-s_94PWmPLfvXoOBuJE4xMts7aVkwNMTBwuPW5yaS12jYJIma0HHWlFb0O6gwGEUrRImBVOWaFQu5DPrRXiienhRRdsvWvyD0HEnKYHWBpL3ZKw4rpVtt1OTqwYqL7dHeSfi2A-i217Tqv3N6MqjOX0DlE3f-CYg"/>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Achmad Fauzi, S.Kom., M.Eng.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19850412 201012 1 002</span>
<span class="font-code-sm text-code-sm text-secondary truncate">achmad.fauzi@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">Information Systems Bureau</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Babarsari Campus</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-primary-container text-on-primary font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">security</span>
<span>Superadmin</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Full Access (Level 0)</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">5 Active Projects (Owner)</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">SI Kampus v3.0</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">API Gateway</span>
<span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface font-code-sm text-code-sm">+3</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active (Google Auth)</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: 5 min ago</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 2: Siti Nurhaliza -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-low/30">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    SN
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Siti Nurhaliza, S.Kom.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19920815 201903 2 007</span>
<span class="font-code-sm text-code-sm text-secondary truncate">siti.nurhaliza@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">BSI Core Backend</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Babarsari Campus</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">code</span>
<span>Lead Backend</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Deploy Staging &amp; Code Review</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">3 Active Projects</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">SI Kampus v3.0</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Payment Gateway</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">SSO</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: 12 min ago</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 3: Dimas Arya -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-lowest">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-primary-fixed text-primary-container flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    DA
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Dimas Arya Pradana, S.T.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19901103 201801 1 004</span>
<span class="font-code-sm text-code-sm text-secondary truncate">dimas.arya@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">Infrastructure &amp; DevOps</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">dns</span>
<span class="font-code-sm text-code-sm">Babarsari Data Center</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-surface-variant text-on-surface font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">terminal</span>
<span>DevOps &amp; Infra</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">K8s Cluster &amp; DB Server</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">4 System Projects</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Kubernetes Cluster</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Hardening</span>
<span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface font-code-sm text-code-sm">+2</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">shield</span>
<span>2FA Mandatory Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: 1 hour ago</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 4: Rizky Pratama -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-low/30">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-surface-container text-secondary flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    RP
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Rizky Pratama, S.Kom.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19950622 202204 1 009</span>
<span class="font-code-sm text-code-sm text-secondary truncate">rizky.pratama@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">Frontend &amp; Interface</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Condongcatur Campus</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed-dim text-on-secondary-fixed-variant font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">web</span>
<span>Frontend Engineer</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Commit &amp; Merge GitLab</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">2 Active Projects</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">SI Kampus v3.0</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Student KRS Portal</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: 2 hours ago</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 5: Budi Santoso -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-lowest">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    BS
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Budi Santoso, M.Kom.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19880210 201504 1 003</span>
<span class="font-code-sm text-code-sm text-secondary truncate">budi.santoso@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">QA &amp; Software Quality</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Babarsari Campus</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">fact_check</span>
<span>QA &amp; Testing Lead</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Bug Triage &amp; Acceptance</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">3 Projects (Sprint)</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">All Active Sprints</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Regression Suite</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: Yesterday, 16:30</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 6: Maya Anggraini -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-low/30">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    MA
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Maya Anggraini, S.Ds.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19961204 202302 2 011</span>
<span class="font-code-sm text-code-sm text-secondary truncate">maya.anggraini@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">Product Design &amp; UI/UX</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Information Systems Bureau</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">palette</span>
<span>UI/UX Designer</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Design System &amp; Assets</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">2 Active Projects</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">Design System UPN</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">SI Kampus v3.0</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: Yesterday, 14:15</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- Row 7: Ir. H. Bambang Sudarsono -->
<tr class="hover:bg-surface-container-low transition-colors bg-surface-container-lowest">
<td class="py-3 px-space-lg text-center">
<input class="w-4 h-4 rounded cursor-pointer accent-secondary" type="checkbox"/>
</td>
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm min-w-[240px]">
<div class="w-9 h-9 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-title-sm text-title-sm font-bold shrink-0">
                    BS
                  </div>
<div class="flex flex-col min-w-0">
<span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">Ir. H. Bambang Sudarsono, M.T.</span>
<span class="font-code-sm text-code-sm text-on-surface-variant font-medium">NIP. 19680517 199403 1 001</span>
<span class="font-code-sm text-code-sm text-secondary truncate">bambang.sudarsono@@upnyk.ac.id</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-medium text-body-medium text-on-surface font-medium">Dean's Office, Fac. of Industrial Engineering</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[13px] text-outline">location_on</span>
<span class="font-code-sm text-code-sm">Babarsari Campus</span>
</div>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col items-start gap-1">
<span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-default text-label-default font-semibold inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">visibility</span>
<span>Faculty PIC / Viewer</span>
</span>
<span class="font-code-sm text-code-sm text-on-surface-variant">Read-Only &amp; Approval Milestone</span>
</div>
</td>
<td class="py-3 px-space-md">
<div class="flex flex-col gap-1 min-w-[200px]">
<span class="font-body-medium text-body-medium text-on-surface font-semibold">1 Faculty Module</span>
<div class="flex flex-wrap gap-1">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm">FTI KRS Module</span>
</div>
</div>
</td>
<td class="py-3 px-space-md whitespace-nowrap">
<div class="flex flex-col">
<div class="inline-flex items-center gap-1 text-on-tertiary-container font-label-default text-label-default font-semibold">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>2FA Active</span>
</div>
<span class="font-code-sm text-code-sm text-outline">Login: 3 days ago</span>
</div>
</td>
<td class="py-3 px-space-lg text-right whitespace-nowrap">
<div class="inline-flex items-center gap-space-xs">
<button class="px-3 py-1.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-secondary font-body-medium text-body-medium font-semibold transition-colors" type="button">
                    Manage Permissions
                  </button>
<button class="p-1.5 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination & Selection Footer -->
<div class="px-space-lg py-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-sm font-body-sm text-body-sm text-on-surface-variant">
<span>Showing <span class="font-semibold text-on-surface">1 - 7</span> of <span class="font-semibold text-on-surface">28</span> registered personnel</span>
<span class="text-outline">|</span>
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-code-sm text-code-sm font-medium">10 rows per page</span>
</div>
<div class="flex items-center gap-space-xs">
<button class="p-2 rounded-lg bg-surface-container-lowest text-outline opacity-50 cursor-not-allowed" disabled="" type="button">
<span class="material-symbols-outlined text-[18px]">chevron_left</span>
</button>
<button class="w-8 h-8 rounded-lg bg-secondary text-on-secondary font-code-sm text-code-sm font-semibold flex items-center justify-center shadow-sm" type="button">1</button>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface font-code-sm text-code-sm font-medium flex items-center justify-center transition-colors" type="button">2</button>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface font-code-sm text-code-sm font-medium flex items-center justify-center transition-colors" type="button">3</button>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface font-code-sm text-code-sm font-medium flex items-center justify-center transition-colors" type="button">4</button>
<button class="p-2 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Asymmetric Bottom Grid: RBAC Quick Reference & SSO Security Policy -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
<!-- RBAC Quick Matrix Overview (7 Columns) -->
<div class="lg:col-span-7 p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-space-md">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-secondary text-[22px]">view_column</span>
<h2 class="font-headline-md text-headline-md text-on-surface font-bold">Quick Role Permission Matrix</h2>
</div>
<a class="font-label-default text-label-default text-secondary hover:underline font-semibold flex items-center gap-0.5" href="#">
<span>Open Full Configuration</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
            Mapping of data operation rights (CRUD) according to campus task hierarchy and BSI audit compliance of UPN "Veteran" Yogyakarta.
          </p>
<div class="overflow-x-auto">
<table class="w-full text-left font-body-sm text-body-sm">
<thead>
<tr class="bg-surface-container-low font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">
<th class="py-2.5 px-3">Role Level</th>
<th class="py-2.5 px-3 text-center">Create Project</th>
<th class="py-2.5 px-3 text-center">Edit Sprint</th>
<th class="py-2.5 px-3 text-center">Assign Task</th>
<th class="py-2.5 px-3 text-center">Deploy Staging</th>
<th class="py-2.5 px-3 text-center">Audit Log</th>
</tr>
</thead>
<tbody class="divide-y-0 font-code-sm text-code-sm">
<tr class="hover:bg-surface-container-low/50">
<td class="py-2.5 px-3 font-semibold text-on-surface flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<span>Superadmin / PM</span>
</td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
</tr>
<tr class="hover:bg-surface-container-low/50 bg-surface-container-low/20">
<td class="py-2.5 px-3 font-semibold text-on-surface flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-secondary"></span>
<span>Lead Developer / DevOps</span>
</td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
</tr>
<tr class="hover:bg-surface-container-low/50">
<td class="py-2.5 px-3 font-semibold text-on-surface flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-secondary-fixed-dim"></span>
<span>Frontend &amp; QA Engineer</span>
</td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-on-tertiary-container"><span class="material-symbols-outlined text-[18px]">check</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
</tr>
<tr class="hover:bg-surface-container-low/50 bg-surface-container-low/20">
<td class="py-2.5 px-3 font-semibold text-on-surface flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-surface-container-highest"></span>
<span>Faculty Unit PIC / Dean's Office</span>
</td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
<td class="py-2.5 px-3 text-center text-outline"><span class="material-symbols-outlined text-[18px]">remove</span></td>
</tr>
</tbody>
</table>
</div>
</div>
<div class="mt-4 pt-3 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant bg-surface-container-low px-3 py-2 rounded-lg">
<span class="flex items-center gap-1 font-label-default text-label-default">
<span class="material-symbols-outlined text-[16px] text-secondary">info</span>
<span>Permission changes require multi-factor authorization from the BSI Cybersecurity Coordinator.</span>
</span>
<button class="text-secondary font-semibold hover:underline font-label-default text-label-default" type="button">Permission Log</button>
</div>
</div>
<!-- Security Policies & SSO Enforcement Card (5 Columns) -->
<div class="lg:col-span-5 p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-space-md">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-primary-container text-[22px]">gshield</span>
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
<span class="material-symbols-outlined text-secondary text-[20px] mt-0.5">key</span>
<div class="flex flex-col">
<span class="font-body-medium text-body-medium font-semibold text-on-surface">Enforced Two-Factor Authentication (2FA)</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Mandatory authentication via Google Authenticator or FIDO2 Key.</span>
</div>
</div>
<span class="text-on-tertiary-container font-label-default text-label-default font-semibold shrink-0">Active</span>
</div>
<!-- Policy Item 2 -->
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start justify-between gap-space-sm">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-secondary text-[20px] mt-0.5">timer</span>
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
<span class="material-symbols-outlined text-secondary text-[20px] mt-0.5">lan</span>
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
<span class="material-symbols-outlined text-[15px]">verified</span>
<span>ISO/IEC 27001 Audit Compliant</span>
</div>
<button class="font-label-default text-label-default text-secondary hover:underline font-semibold" type="button">
            Advanced Security Configuration
          </button>
</div>
</div>
</div>
</div>
</div>
@endsection
