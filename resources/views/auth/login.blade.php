<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in — UPN Task Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-on-surface font-body-default text-body-default antialiased min-h-screen">
<div class="min-h-screen grid lg:grid-cols-2">
    {{-- Brand panel --}}
    <div class="hidden lg:flex flex-col justify-between bg-primary text-on-primary p-space-2xl relative overflow-hidden">
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-secondary/20 blur-3xl"></div>
        <div class="absolute -left-16 bottom-10 w-72 h-72 rounded-full bg-tertiary-fixed/10 blur-3xl"></div>
        <div class="flex items-center gap-space-md relative">
            <div class="h-10 w-10 rounded-xl bg-surface-container-lowest text-primary flex items-center justify-center font-headline-md text-headline-md font-bold">U</div>
            <div class="flex flex-col leading-none">
                <span class="font-title-sm text-title-sm tracking-tight">UPN Veteran</span>
                <span class="font-label-uppercase text-label-uppercase uppercase tracking-wider opacity-70 mt-0.5">Task Management</span>
            </div>
        </div>
        <div class="relative max-w-md">
            <h1 class="font-headline-xl text-headline-xl tracking-tight">Manage tasks, projects, and teams in one place.</h1>
            <div class="mt-space-lg flex flex-col gap-space-sm">
                <div class="flex items-center gap-space-sm opacity-90">
                    <x-rune-icon name="indicators-circle-check" class="w-[20px] h-[20px]" />
                    <span class="font-body-medium text-body-medium">Track project progress in real time</span>
                </div>
                <div class="flex items-center gap-space-sm opacity-90">
                    <x-rune-icon name="identity-users" class="w-[20px] h-[20px]" />
                    <span class="font-body-medium text-body-medium">Team collaboration with comments &amp; attachments</span>
                </div>
                <div class="flex items-center gap-space-sm opacity-90">
                    <x-rune-icon name="metrics-chart-bar" class="w-[20px] h-[20px]" />
                    <span class="font-body-medium text-body-medium">Productivity and member workload reports</span>
                </div>
            </div>
        </div>
        <p class="font-label-default text-label-default opacity-60 relative">Universitas Pembangunan Nasional “Veteran” Yogyakarta</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center p-space-lg">
        <div class="w-full max-w-md">
            <div class="flex lg:hidden items-center gap-space-md mb-space-xl">
                <div class="h-10 w-10 rounded-xl bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold">U</div>
                <div class="flex flex-col leading-none">
                    <span class="font-title-sm text-title-sm text-on-surface tracking-tight">UPN Veteran</span>
                    <span class="font-label-uppercase text-label-uppercase text-on-surface-variant uppercase tracking-wider mt-0.5">Task Management</span>
                </div>
            </div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Welcome back</h2>
            <p class="font-body-default text-body-default text-on-surface-variant mt-1">Sign in to continue to your workspace.</p>

            <form method="post" action="{{ route('login') }}" class="mt-space-xl flex flex-col gap-space-md">
                @csrf
                <div class="flex flex-col gap-space-xs">
                    <label for="email" class="font-label-default text-label-default font-medium text-on-surface">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="name@upnyk.ac.id"
                        class="h-9 px-space-md bg-surface-container-lowest border border-outline-variant/60 rounded-xl font-body-sm text-body-sm text-on-surface placeholder:text-outline outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/15 transition-all @error('email') border-error @enderror" />
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label for="password" class="font-label-default text-label-default font-medium text-on-surface">Password</label>
                    <input id="password" name="password" type="password" required placeholder="••••••••"
                        class="h-9 px-space-md bg-surface-container-lowest border border-outline-variant/60 rounded-xl font-body-sm text-body-sm text-on-surface placeholder:text-outline outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/15 transition-all @error('password') border-error @enderror" />
                </div>
                @error('email')
                    <p class="flex items-center gap-1 font-body-sm text-body-sm text-error">
                        <x-rune-icon name="indicators-circle-x" class="w-[16px] h-[16px]" /> {{ $message }}
                    </p>
                @enderror
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant cursor-pointer">
                        <input name="remember" type="checkbox" value="1" class="rounded accent-secondary w-4 h-4" />
                        Remember me
                    </label>
                </div>
                <button type="submit" class="h-9 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors shadow-sm active:scale-[0.99]">
                    Sign in
                </button>
            </form>

            @if ($devAccounts->isNotEmpty())
                <div class="mt-space-lg p-space-md bg-surface-container-low rounded-xl">
                    <p class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Demo accounts (click to autofill — local only)</p>
                    <div class="mt-space-sm flex flex-col gap-space-xs">
                        @foreach ($devAccounts as $account)
                            <button type="button" data-email="{{ $account['email'] }}"
                                onclick="document.getElementById('email').value = this.dataset.email; document.getElementById('password').value = 'password';"
                                class="flex items-center justify-between gap-space-sm px-space-sm py-1.5 bg-surface-container-lowest rounded-lg hover:bg-surface-container transition-colors text-left">
                                <span class="min-w-0">
                                    <span class="block font-body-sm text-body-sm font-medium text-on-surface truncate">{{ $account['name'] }}</span>
                                    <span class="block font-code-sm text-code-sm text-outline truncate">{{ $account['email'] }} • password: <code>password</code></span>
                                </span>
                                <span class="shrink-0 px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-default text-label-default font-semibold">{{ str($account['role'])->replace('_', ' ')->title() }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
