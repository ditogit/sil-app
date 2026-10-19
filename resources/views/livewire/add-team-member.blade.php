<div>
    @if ($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-space-lg overflow-y-auto" x-data x-on:keydown.escape.window="$wire.close()">
        <div wire:click="close" class="fixed inset-0 bg-on-surface/35 backdrop-blur-[3px]"></div>
        <div class="relative w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
            <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex items-start justify-between gap-space-md shrink-0">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                        <x-heroicon-o-user-plus class="w-[22px] h-[22px]" />
                    </div>
                    <div class="flex flex-col">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Add Team Member</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Provision a campus account and assign an RBAC role</p>
                    </div>
                </div>
                <button wire:click="close" type="button" title="Close (Esc)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm shrink-0 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-space-xl py-space-lg overflow-y-auto flex flex-col gap-space-md">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addName">Full Name</label>
                        <input wire:model="name" id="addName" type="text" placeholder="E.g.: Achmad Fauzi, S.Kom." class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('name') ring-2 ring-error @enderror" />
                        @error('name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addNip">NIP / NIDN</label>
                        <input wire:model="nip" id="addNip" type="text" placeholder="19850412 201012 1 002" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('nip') ring-2 ring-error @enderror" />
                        @error('nip')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addEmail">Institutional Email</label>
                    <input wire:model="email" id="addEmail" type="email" placeholder="name@upnyk.ac.id" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('email') ring-2 ring-error @enderror" />
                    @error('email')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addPassword">Temporary Password</label>
                        <input wire:model="password" id="addPassword" type="text" placeholder="Min. 8 characters" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('password') ring-2 ring-error @enderror" />
                        @error('password')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addPosition">Position / Expertise</label>
                        <input wire:model="position" id="addPosition" type="text" placeholder="Lead Backend" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm @error('position') ring-2 ring-error @enderror" />
                        @error('position')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addUnit">Work Unit</label>
                        <select wire:model="work_unit_id" id="addUnit" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25 shadow-sm">
                            <option value="">Unassigned</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addCampus">Campus / Location</label>
                        <input wire:model="campus" id="addCampus" type="text" placeholder="Babarsari Campus" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 shadow-sm" />
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="addRole">RBAC Role</label>
                        <select wire:model="role_name" id="addRole" class="h-10 px-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25 shadow-sm @error('role_name') ring-2 ring-error @enderror">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-space-sm pt-2 shrink-0">
                    <button wire:click="close" type="button" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shadow-sm cursor-pointer">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Add Member</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
