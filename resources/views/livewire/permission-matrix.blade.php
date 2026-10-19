<div>
    @if ($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-space-lg overflow-y-auto" x-data x-on:keydown.escape.window="$wire.close()">
        <div wire:click="close" class="fixed inset-0 bg-on-surface/35 backdrop-blur-[3px]"></div>
        <div class="relative w-full max-w-3xl bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
            <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex items-start justify-between gap-space-md shrink-0">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                        <x-heroicon-o-shield-check class="w-[22px] h-[22px]" />
                    </div>
                    <div class="flex flex-col">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Role Permission Matrix</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Assign the CRUD rights held by each RBAC role</p>
                    </div>
                </div>
                <button wire:click="close" type="button" title="Close (Esc)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm shrink-0 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-space-xl py-space-lg overflow-auto flex flex-col gap-space-md">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">
                            <th class="py-2.5 px-3 sticky left-0 bg-surface-container-low">Permission</th>
                            @foreach ($roles as $role)
                                <th class="py-2.5 px-3 text-center">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-body-sm font-body-sm">
                        @foreach ($permissions as $permission)
                            <tr wire:key="perm-{{ $permission->id }}" class="hover:bg-surface-container-low/50">
                                <td class="py-2 px-3 font-code-sm text-code-sm text-on-surface sticky left-0 bg-surface-container-lowest">{{ $permission->name }}</td>
                                @foreach ($roles as $role)
                                    <td class="py-2 px-3 text-center">
                                        <input wire:model="selected.{{ $role->name }}" type="checkbox" value="{{ $permission->name }}" class="w-4 h-4 rounded accent-secondary cursor-pointer" />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex items-center justify-end gap-space-sm pt-2 shrink-0">
                    <button wire:click="close" type="button" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shadow-sm cursor-pointer">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Save Matrix</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
