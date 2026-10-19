<div>
    @if ($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-space-lg overflow-y-auto" x-data x-on:keydown.escape.window="$wire.close()">
        <div wire:click="close" class="fixed inset-0 bg-on-surface/35 backdrop-blur-[3px]"></div>
        <div class="relative w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
            <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex items-start justify-between gap-space-md shrink-0">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                        <x-rune-icon name="documents-file-plus" class="w-[22px] h-[22px]" />
                    </div>
                    <div class="flex flex-col">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Add Personal Task</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Quickly create a task for yourself</p>
                    </div>
                </div>
                <button wire:click="close" type="button" title="Close (Esc)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm shrink-0">
                    <x-rune-icon name="indicators-x" class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-space-xl py-space-lg overflow-y-auto flex flex-col gap-space-md">
                <div class="flex flex-col gap-space-xs">
                    <label class="font-title-sm text-title-sm text-on-surface" for="quickTitle">Task Title <span class="text-error font-bold">*</span></label>
                    <input wire:model="title" id="quickTitle" type="text" maxlength="160" autofocus placeholder="e.g.: Review SIAKAD API document"
                        class="w-full h-11 px-space-md bg-surface-container-low rounded-xl font-body-medium text-body-medium outline-none focus:bg-surface-container focus:ring-2 focus:ring-secondary/25 shadow-sm @error('title') ring-2 ring-error @enderror" />
                    @error('title')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickProject">Project <span class="text-error">*</span></label>
                        <select wire:model.live="project_id" id="quickProject" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickMilestone">Milestone</label>
                        <select wire:model="milestone_id" id="quickMilestone" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none cursor-pointer focus:ring-2 focus:ring-secondary/25">
                            <option value="">— No milestone —</option>
                            @foreach ($milestones as $milestone)
                                <option value="{{ $milestone->id }}">{{ $milestone->title }}</option>
                            @endforeach
                        </select>
                        @error('milestone_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Priority</label>
                    <div class="grid grid-cols-4 gap-1.5 p-1 bg-surface-container rounded-xl">
                        @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                            <button type="button" wire:click="setPriority('{{ $value }}')"
                                class="flex items-center justify-center gap-1 py-1.5 rounded-lg text-label-default font-label-default transition-all {{ $priority === $value ? ($value === 'critical' ? 'bg-error text-on-error font-semibold shadow-sm' : 'bg-surface-container-lowest text-error font-semibold shadow-sm') : 'text-on-surface-variant hover:bg-surface-container-lowest' }}">
                                @if ($value === 'critical')
                                    <x-rune-icon name="other-zap" class="w-[13px] h-[13px] {{ $priority === $value ? '' : 'text-error' }}" />
                                @else
                                    <span class="w-2 h-2 rounded-full {{ $value === 'low' ? 'bg-outline' : ($value === 'medium' ? 'bg-secondary' : 'bg-error') }}"></span>
                                @endif
                                <span>{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-space-sm">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickDue">Due date <span class="text-error">*</span></label>
                        <input wire:model="due_date" id="quickDue" type="date" min="{{ today()->toDateString() }}"
                            class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('due_date') ring-2 ring-error @enderror" />
                        @error('due_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickTime">Time</label>
                        <input wire:model="due_time" id="quickTime" type="time"
                            class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                    </div>
                </div>

                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="quickDesc">Description</label>
                    <textarea wire:model="description" id="quickDesc" rows="3" placeholder="Short detail (optional)..."
                        class="w-full p-space-md bg-surface-container-low rounded-xl font-body-sm text-body-sm outline-none resize-y focus:bg-surface-container focus:ring-2 focus:ring-secondary/25 shadow-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-space-sm pt-2 shrink-0">
                    <button wire:click="close" type="button" class="h-10 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors shadow-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md active:scale-[0.98] disabled:opacity-60 flex items-center gap-space-xs">
                        <x-rune-icon name="indicators-plus" class="w-[18px] h-[18px]" wire:loading.remove />
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Create Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
