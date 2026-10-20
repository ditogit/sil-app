<div class="px-space-xl py-space-lg flex flex-col gap-space-lg max-w-[1200px] w-full mx-auto">
    <div class="flex flex-col gap-space-xxs min-w-0">
        <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Settings</h1>
        <p class="font-body-default text-body-default text-on-surface-variant">Manage your account profile, access role, and sign-in security.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg items-start">
        <div class="lg:col-span-2 flex flex-col gap-space-lg">
            <form wire:submit="updateProfile" class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
                <div class="flex flex-col gap-space-xxs">
                    <h2 class="font-title-lg text-title-lg text-on-surface">Profile</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Your name and institutional email are visible to project members you collaborate with.</p>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="settingsName">Full Name</label>
                    <input wire:model="name" id="settingsName" type="text" placeholder="E.g.: Achmad Fauzi, S.Kom." class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('name') ring-2 ring-error @enderror" />
                    @error('name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="settingsEmail">Institutional Email</label>
                    <input wire:model="email" id="settingsEmail" type="email" placeholder="name@upnyk.ac.id" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('email') ring-2 ring-error @enderror" />
                    @error('email')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Save Profile</span>
                    </button>
                </div>
            </form>

            <form wire:submit="updatePassword" class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
                <div class="flex flex-col gap-space-xxs">
                    <h2 class="font-title-lg text-title-lg text-on-surface">Password</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Use at least 8 characters. Your current password is required to confirm the change.</p>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="settingsCurrentPassword">Current Password</label>
                    <input wire:model="current_password" id="settingsCurrentPassword" type="password" autocomplete="current-password" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('current_password') ring-2 ring-error @enderror" />
                    @error('current_password')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="settingsPassword">New Password</label>
                        <input wire:model="password" id="settingsPassword" type="password" autocomplete="new-password" placeholder="Min. 8 characters" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('password') ring-2 ring-error @enderror" />
                        @error('password')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="settingsPasswordConfirmation">Confirm New Password</label>
                        <input wire:model="password_confirmation" id="settingsPasswordConfirmation" type="password" autocomplete="new-password" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Update Password</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
            <h2 class="font-title-lg text-title-lg text-on-surface">Access</h2>

            <div class="flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl {{ $roleMeta['badge'] }} flex items-center justify-center font-headline-md text-headline-md font-bold shrink-0">{{ $user->initials() }}</div>
                <div class="flex flex-col min-w-0">
                    <span class="font-body-medium text-body-medium text-on-surface font-semibold truncate">{{ $user->name }}</span>
                    <span class="font-label-default text-label-default text-outline truncate">{{ $user->email }}</span>
                </div>
            </div>

            <dl class="flex flex-col gap-space-sm">
                <div class="flex items-center justify-between gap-space-sm">
                    <dt class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Role</dt>
                    <dd class="font-body-sm text-body-sm text-on-surface text-right">{{ $roleMeta['label'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-space-sm">
                    <dt class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Account Status</dt>
                    <dd class="font-body-sm text-body-sm text-on-surface text-right">{{ $securityStatus['label'] }}</dd>
                </div>
                @if ($user->nip)
                    <div class="flex items-center justify-between gap-space-sm">
                        <dt class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">NIP / NIDN</dt>
                        <dd class="font-body-sm text-body-sm text-on-surface text-right">{{ $user->nip }}</dd>
                    </div>
                @endif
                @if ($user->position)
                    <div class="flex items-center justify-between gap-space-sm">
                        <dt class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Position</dt>
                        <dd class="font-body-sm text-body-sm text-on-surface text-right">{{ $user->position }}</dd>
                    </div>
                @endif
            </dl>

            <p class="font-body-sm text-body-sm text-on-surface-variant">Role changes are managed by administrators on the team settings screen.</p>
        </div>
    </div>
</div>
