@extends(auth()->check() ? 'layouts.app' : 'layouts.guest', ['title' => 'Access restricted', 'breadcrumb' => 'Access restricted'])

@section('content')
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-space-md py-space-xl">
        <div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-sm p-space-xl flex flex-col items-center text-center gap-space-md">
            <div class="w-14 h-14 rounded-2xl bg-error-container text-on-error-container flex items-center justify-center">
                <x-rune-icon name="identity-shield" class="w-7 h-7" />
            </div>
            <div class="flex flex-col gap-1">
                <h1 class="font-headline-md text-headline-md text-on-surface">Access restricted</h1>
                <p class="font-body-default text-body-default text-on-surface-variant">
                    You don’t have permission to open this page. If you think this is a mistake, please contact your workspace administrator.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-space-sm pt-1">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-space-xs h-9 px-space-md rounded-xl bg-primary-container text-on-primary font-body-medium text-body-medium shadow-sm hover:bg-primary transition-all">
                    <x-rune-icon name="layouts-grid-2x2" class="w-[18px] h-[18px]" />
                    <span>Back to Dashboard</span>
                </a>
                <button type="button" onclick="history.back()"
                    class="inline-flex items-center gap-space-xs h-9 px-space-md rounded-xl bg-surface-container text-on-surface font-body-medium text-body-medium hover:bg-surface-container-high transition-all">
                    <x-rune-icon name="arrows-chevron-right" class="w-[18px] h-[18px] rotate-180" />
                    <span>Go back</span>
                </button>
            </div>
        </div>
    </div>
@endsection
