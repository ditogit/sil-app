<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? trim($__env->yieldContent('title')) ?: 'Dashboard' }} — Task Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-surface text-on-surface font-body-default text-body-default antialiased">
    <div x-data="{ sidebarOpen: false }">
        {{-- Sidebar --}}
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 bg-inverse-surface/40 z-40 lg:hidden" x-on:click="sidebarOpen = false"></div>
        <aside :class="sidebarOpen ? 'flex' : 'hidden'" class="fixed left-0 top-0 h-full w-64 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 lg:flex flex-col justify-between select-none print:hidden">
            <div class="flex flex-col min-h-0">
                <div class="px-space-lg pt-space-lg pb-space-md flex items-center gap-space-md">
                    <div class="h-8 w-8 rounded-lg bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold shrink-0">T</div>
                    <div class="flex flex-col min-w-0 leading-none">
                        <span class="font-title-sm text-title-sm text-on-surface truncate tracking-tight">My Task</span>
                        <span class="font-label-uppercase text-label-uppercase text-on-surface-variant uppercase tracking-wider mt-0.5">Task Management</span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div class="px-space-lg pt-space-md pb-space-xs">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Main Menu</span>
                    </div>
                    <nav class="flex flex-col gap-0.5 px-space-md">
                        @foreach ($navMain as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center justify-between px-space-md py-2 rounded-xl transition-all {{ $item['active'] ? 'bg-surface-container-low text-secondary font-semibold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                            <div class="flex items-center gap-space-md">
                                <x-rune-icon name="{{ $item['icon'] }}" class="w-5 h-5" />
                                <span class="font-body-medium text-body-medium">{{ $item['label'] }}</span>
                            </div>
                            @if (! empty($item['badge']))
                            <span class="px-1.5 py-0.5 rounded-full {{ $item['badge_tone'] === 'red' ? 'bg-error text-on-error font-semibold' : 'bg-surface-container text-on-surface-variant' }} font-label-default text-label-default">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                        @endforeach
                    </nav>
                    <div class="px-space-md pt-space-md">
                        <nav class="flex flex-col gap-0.5">
                            @foreach ($navSecondary as $item)
                            <a href="{{ route($item['route']) }}" class="flex items-center justify-between px-space-md py-2 rounded-xl transition-all {{ $item['active'] ? 'bg-surface-container-low text-secondary font-semibold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                                <div class="flex items-center gap-space-md">
                                    <x-rune-icon name="{{ $item['icon'] }}" class="w-5 h-5" />
                                    <span class="font-body-medium text-body-medium">{{ $item['label'] }}</span>
                                </div>
                                @if (! empty($item['badge']))
                                <span class="px-1.5 py-0.5 rounded-full bg-error text-on-error font-label-default text-label-default font-semibold leading-none">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                            @endforeach
                        </nav>
                    </div>
                    <div class="px-space-lg pt-space-md pb-space-xs">
                        <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Administration</span>
                    </div>
                    <nav class="flex flex-col gap-0.5 px-space-md">
                        @foreach ($navAdmin as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center justify-between px-space-md py-2 rounded-xl transition-all {{ $item['active'] ? 'bg-surface-container-low text-secondary font-semibold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                            <div class="flex items-center gap-space-md">
                                <x-rune-icon name="{{ $item['icon'] }}" class="w-[20px] h-[20px]" />
                                <span class="font-body-medium text-body-medium">{{ $item['label'] }}</span>
                            </div>
                        </a>
                        @endforeach
                    </nav>
                </div>
            </div>
            <div class="p-space-md bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] m-space-sm rounded-xl">
                <div class="flex items-center justify-between gap-space-sm">
                    <div class="flex items-center gap-space-sm min-w-0">
                        <div class="w-8 h-8 rounded-full {{ $userTint }} flex items-center justify-center font-label-default text-label-default font-bold shrink-0">{{ $userInitials }}</div>
                        <div class="flex flex-col min-w-0 leading-tight">
                            <span class="font-body-medium text-body-medium text-on-surface font-semibold truncate">{{ $shellUser->name }}</span>
                            <span class="font-label-uppercase text-label-uppercase text-on-surface-variant truncate">{{ $shellRole }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Content column --}}
        <div class="lg:pl-64">
            <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-space-md lg:px-space-xl print:hidden">
                <div class="flex items-center gap-space-md min-w-0">
                    <button x-on:click="sidebarOpen = true" type="button" class="lg:hidden p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-xl transition-colors">
                        <x-rune-icon name="layouts-menu" class="w-[20px] h-[20px]" />
                    </button>
                    <div class="hidden sm:flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm whitespace-nowrap">
                        <span>Workspace</span>
                        <x-rune-icon name="arrows-chevron-right" class="w-[14px] h-[14px]" />
                        <span class="text-on-surface font-medium">{{ $breadcrumb ?? trim($__env->yieldContent('breadcrumb')) ?: 'Main Dashboard' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-space-md">
                    <a href="{{ route('notifications') }}" class="relative p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-xl transition-colors" title="Notifications">
                        <x-rune-icon name="messaging-message-circle" class="w-[20px] h-[20px]" />
                        @if ($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
                        @endif
                    </a>
                    <a href="{{ route('settings') }}" class="p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-xl transition-colors" title="Help &amp; Settings">
                        <x-rune-icon name="indicators-circle-question-mark" class="w-[20px] h-[20px]" />
                    </a>
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = ! open" type="button" class="flex items-center gap-space-xs pl-space-xs cursor-pointer">
                            <div class="w-8 h-8 rounded-full {{ $userTint }} flex items-center justify-center font-label-default text-label-default font-bold shrink-0">{{ $userInitials }}</div>
                            <x-rune-icon name="arrows-chevron-down" class="text-outline w-[16px] h-[16px]" />
                        </button>
                        <div x-show="open" x-cloak x-on:click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/40 overflow-hidden z-50">
                            <div class="px-space-md py-space-sm">
                                <p class="font-body-medium text-body-medium font-semibold text-on-surface truncate">{{ $shellUser->name }}</p>
                                <p class="font-label-default text-label-default text-outline truncate">{{ $shellUser->email }}</p>
                            </div>
                            <a href="{{ route('settings') }}" class="block px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface">Settings</a>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container/30">Log out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="relative pt-16 print:pt-0 w-full min-h-screen bg-surface print:bg-white">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed bottom-6 right-6 z-[60] flex items-center gap-space-sm px-space-md py-space-sm bg-tertiary-container text-on-tertiary rounded-xl shadow-lg">
                <span class="font-body-medium text-body-medium">{{ session('success') }}</span>
                <button x-on:click="show = false" type="button" class="text-on-tertiary/70 hover:text-on-tertiary">
                    <x-rune-icon name="indicators-x" class="w-4 h-4" />
                </button>
            </div>
            @endif
        </div>
    </div>
    @livewireScripts
    @yield('scripts')
</body>

</html>
