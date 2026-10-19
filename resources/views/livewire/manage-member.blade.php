<div>
    @if ($open && $user)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-space-lg overflow-y-auto" x-data x-on:keydown.escape.window="$wire.close()">
        <div wire:click="close" class="fixed inset-0 bg-on-surface/35 backdrop-blur-[3px]"></div>
        <div class="relative w-full max-w-xl bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
            <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex items-start justify-between gap-space-md shrink-0">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-title-sm text-title-sm font-bold shadow-sm">{{ $user->initials() }}</div>
                    <div class="flex flex-col">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Manage Permissions</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $user->name }} • {{ $user->email }}</p>
                    </div>
                </div>
                <button wire:click="close" type="button" title="Close (Esc)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm shrink-0 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-space-xl py-space-lg overflow-y-auto flex flex-col gap-space-md">
                <div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between gap-space-sm text-body-sm font-body-sm">
                    <span class="flex items-center gap-1 text-on-surface-variant">
                        <x-heroicon-o-building-office-2 class="w-4 h-4 text-outline" />
                        {{ $user->workUnit?->name ?? 'Unassigned' }}
                    </span>
                    <span class="font-code-sm text-code-sm text-outline">{{ $user->nip ? 'NIP. '.$user->nip : 'No NIP' }}</span>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="manageRole">RBAC Role</label>
                    <select wire:model="role_name" id="manageRole" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25 shadow-sm @error('role_name') ring-2 ring-error @enderror">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="manageStatus">Access Status</label>
                    <select wire:model="status" id="manageStatus" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25 shadow-sm">
                        <option value="active">Active</option>
                        <option value="pending_2fa">Pending 2FA</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <label class="flex items-center gap-space-sm p-space-md rounded-xl bg-surface-container-low cursor-pointer">
                    <input wire:model="two_factor" type="checkbox" class="w-4 h-4 rounded accent-secondary" />
                    <span class="flex flex-col">
                        <span class="font-body-medium text-body-medium font-semibold text-on-surface">Two-Factor Authentication enrolled</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Marks the account as 2FA compliant.</span>
                    </span>
                </label>

                <div class="flex items-center justify-end gap-space-sm pt-2 shrink-0">
                    <button wire:click="close" type="button" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shadow-sm cursor-pointer">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
