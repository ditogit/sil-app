<div>
    @if ($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-space-lg overflow-y-auto" x-data x-on:keydown.escape.window="$wire.close()">
        <div wire:click="close" class="fixed inset-0 bg-on-surface/35 backdrop-blur-[3px]"></div>
        <div class="relative w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
            <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex items-start justify-between gap-space-md shrink-0">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                        <x-rune-icon name="documents-folder" class="w-[22px] h-[22px]" />
                    </div>
                    <div class="flex flex-col">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Create New Project</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">New workspace (you are added automatically)</p>
                    </div>
                </div>
                <button wire:click="close" type="button" title="Close (Esc)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm shrink-0">
                    <x-rune-icon name="indicators-x" class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-space-xl py-space-lg overflow-y-auto flex flex-col gap-space-md">
                <div class="flex flex-col gap-space-xs">
                    <label class="font-title-sm text-title-sm text-on-surface" for="quickProjectName">Project Name <span class="text-error font-bold">*</span></label>
                    <input wire:model="name" id="quickProjectName" type="text" maxlength="255" autofocus placeholder="E.g.: Campus Information System v3.0"
                        class="w-full h-11 px-space-md bg-surface-container-low rounded-xl font-body-medium text-body-medium outline-none focus:bg-surface-container focus:ring-2 focus:ring-secondary/25 shadow-sm @error('name') ring-2 ring-error @enderror" />
                    @error('name')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickProjectDesc">Description</label>
                    <textarea wire:model="description" id="quickProjectDesc" rows="3" placeholder="Project goals and scope..."
                        class="w-full p-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none resize-y focus:bg-surface-container focus:ring-2 focus:ring-secondary/25 shadow-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-space-sm">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickStart">Start Date <span class="text-error">*</span></label>
                        <input wire:model="start_date" id="quickStart" type="date"
                            class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('start_date') ring-2 ring-error @enderror" />
                        @error('start_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickEnd">End Date</label>
                        <input wire:model="end_date" id="quickEnd" type="date"
                            class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('end_date') ring-2 ring-error @enderror" />
                        @error('end_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <span class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Initial Members</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-xs max-h-48 overflow-y-auto p-space-sm bg-surface-container-low rounded-xl">
                        @foreach ($users as $user)
                            <label class="flex items-center gap-space-sm p-space-xs rounded-lg hover:bg-surface-container-lowest cursor-pointer transition-colors">
                                <input wire:model="member_ids" type="checkbox" value="{{ $user->id }}" class="w-4 h-4 rounded accent-secondary shrink-0" />
                                <span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center text-[10px] font-bold shrink-0">{{ $user->initials() }}</span>
                                <span class="font-body-sm text-body-sm font-medium text-on-surface truncate">{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-space-sm pt-2 shrink-0">
                    <button wire:click="close" type="button" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shadow-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60">
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Create Project</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
