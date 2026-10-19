<div class="px-space-xl py-space-lg max-w-[1600px] mx-auto w-full" x-data="{ titleLen: 0 }">
    <div class="w-full max-w-4xl mx-auto bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="px-space-xl pt-space-lg pb-space-md bg-surface-container-low flex flex-col gap-space-xs">
            <div class="flex items-start justify-between gap-space-md">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                        <x-rune-icon name="documents-file-plus" class="w-[22px] h-[22px]" />
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-sm">
                            <h2 class="font-headline-lg text-headline-lg text-on-surface">Create New Task</h2>
                            @if ($activeProject)
                                <span class="font-label-uppercase text-label-uppercase bg-secondary-fixed text-on-secondary-fixed-variant px-space-xs py-0.5 rounded-lg">{{ str($activeProject->name)->words(3, '') }}</span>
                            @endif
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Add work tasks to your team's workflow</p>
                    </div>
                </div>
                <div class="flex items-center gap-space-sm">
                    <div class="relative">
                        <select wire:model.live="project_id" class="h-9 pl-9 pr-9 bg-surface-container-lowest text-on-surface rounded-xl shadow-sm font-body-medium text-body-medium font-semibold appearance-none outline-none focus:ring-2 focus:ring-secondary/25 cursor-pointer max-w-64">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></span>
                        <x-rune-icon name="arrows-chevron-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-[18px] h-[18px]" />
                    </div>
                    <a href="{{ route('tasks.mine') }}" title="Cancel and go back" class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container-lowest hover:bg-error-container hover:text-on-error-container text-on-surface-variant transition-colors shadow-sm">
                        <x-rune-icon name="indicators-x" class="w-5 h-5" />
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-space-xs pt-space-xs font-label-default text-label-default text-outline">
                <span class="font-semibold text-secondary">{{ $activeProject?->name ?? 'Select project' }}</span>
                <x-rune-icon name="arrows-chevron-right" class="w-[14px] h-[14px]" />
                <span class="text-on-surface">New Task Draft</span>
            </div>
            @error('project_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
        </div>

        <form wire:submit="save" class="flex flex-col">
            {{-- Body --}}
            <div class="px-space-xl py-space-lg flex flex-col gap-space-xl">
                {{-- Title --}}
                <div class="flex flex-col gap-space-xs">
                    <div class="flex items-center justify-between">
                        <label class="font-title-sm text-title-sm text-on-surface" for="taskTitle">Task Title <span class="text-error font-bold">*</span></label>
                        <span class="font-code-sm text-code-sm text-outline"><span x-text="titleLen">0</span> / 160 characters</span>
                    </div>
                    <input wire:model="title" x-on:input="titleLen = $el.value.length" id="taskTitle" type="text" maxlength="160"
                        placeholder="e.g.: SIAKAD SSO fix for Field Supervisor Lecturer"
                        class="w-full h-11 px-space-md bg-surface-container-low text-on-surface rounded-xl font-body-medium text-body-medium outline-none focus:bg-surface-container focus:ring-2 focus:ring-secondary/25 transition-all shadow-sm @error('title') ring-2 ring-error @enderror" />
                    @error('title')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="flex flex-col gap-space-xs" x-data="{ insertMd(ta, before, after = '') { const s = ta.selectionStart, e = ta.selectionEnd, v = ta.value; ta.value = v.slice(0, s) + before + v.slice(s, e) + after + v.slice(e); ta.focus(); ta.dispatchEvent(new Event('input')); } }">
                    <div class="flex items-center justify-between">
                        <label class="font-title-sm text-title-sm text-on-surface">Description &amp; Technical Specification</label>
                        <span class="font-label-default text-label-default text-on-surface-variant flex items-center gap-1">
                            <x-rune-icon name="code-braces" class="w-[14px] h-[14px]" /> Markdown supported
                        </span>
                    </div>
                    <div class="rounded-xl overflow-hidden bg-surface-container-low shadow-sm">
                        <div class="px-space-sm py-1.5 bg-surface-container flex items-center flex-wrap gap-0.5">
                            <button type="button" title="Bold" x-on:click="insertMd($refs.desc, '**', '**')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors font-bold text-[15px] leading-none w-8 h-8">B</button>
                            <button type="button" title="Italic" x-on:click="insertMd($refs.desc, '_', '_')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors italic text-[15px] leading-none w-8 h-8">I</button>
                            <button type="button" title="Bullet List" x-on:click="insertMd($refs.desc, '\n- ')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors">
                                <x-rune-icon name="tools-list-filter" class="w-[18px] h-[18px]" />
                            </button>
                            <button type="button" title="Code Block" x-on:click="insertMd($refs.desc, '\n```\n', '\n```')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors">
                                <x-rune-icon name="code-code" class="w-[18px] h-[18px]" />
                            </button>
                            <button type="button" title="Link" x-on:click="insertMd($refs.desc, '[', '](https://)')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors">
                                <x-rune-icon name="code-link" class="w-[18px] h-[18px]" />
                            </button>
                        </div>
                        <textarea wire:model="description" x-ref="desc" rows="4"
                            placeholder="Write the specification, acceptance criteria, target endpoint, and QA instructions..."
                            class="w-full p-space-md bg-surface-container-low text-on-surface font-body-sm text-body-sm outline-none resize-y focus:bg-surface-container transition-all"></textarea>
                    </div>
                    @error('description')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                </div>

                {{-- Bento grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg bg-surface-container-low p-space-lg rounded-xl">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="milestone">Project Milestone</label>
                        <div class="relative">
                            <select wire:model="milestone_id" id="milestone" class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm appearance-none outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm cursor-pointer pr-10">
                                <option value="">— No milestone —</option>
                                @foreach ($milestones as $milestone)
                                    <option value="{{ $milestone->id }}">{{ $milestone->title }}</option>
                                @endforeach
                            </select>
                            <x-rune-icon name="tools-flag" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-[18px] h-[18px]" />
                        </div>
                        @error('milestone_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="assignee">Assignee <span class="text-error">*</span></label>
                        <div class="relative">
                            <select wire:model="assignee_id" id="assignee" class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm appearance-none outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm cursor-pointer pr-10">
                                <option value="">— Not yet assigned —</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} — {{ str($user->getRoleNames()->first() ?? 'member')->replace('_', ' ')->title() }}</option>
                                @endforeach
                            </select>
                            <x-rune-icon name="arrows-arrow-left-right" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none w-[18px] h-[18px]" />
                        </div>
                        @error('assignee_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Priority Level</label>
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
                        @error('priority')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Due Date</label>
                            <span class="font-label-default text-label-default text-secondary">Time Zone: WIB</span>
                        </div>
                        <div class="grid grid-cols-2 gap-space-sm">
                            <div class="relative">
                                <input wire:model="due_date" type="date" min="{{ today()->toDateString() }}"
                                    class="w-full h-10 pl-10 pr-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('due_date') ring-2 ring-error @enderror" />
                                <x-rune-icon name="schedule-calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none w-[18px] h-[18px]" />
                            </div>
                            <input wire:model="due_time" type="time"
                                class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25 @error('due_time') ring-2 ring-error @enderror" />
                        </div>
                        @error('due_date')
                            <div class="flex items-center gap-1.5 px-space-sm py-1 rounded-lg bg-error-container/40 font-label-default text-label-default">
                                <x-rune-icon name="indicators-circle-x" class="text-error w-4 h-4" />
                                <span class="text-error font-medium">{{ $message }}</span>
                            </div>
                        @else
                            @if ($due_date)
                                <div class="flex items-center gap-1.5 px-space-sm py-1 rounded-lg bg-surface-container-highest font-label-default text-label-default">
                                    <x-rune-icon name="indicators-circle-check" class="text-on-tertiary-container w-4 h-4" />
                                    <span class="font-medium text-tertiary-container">System Validation:</span>
                                    <span class="text-on-surface-variant">Valid due date (D-{{ max(0, (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($due_date)->startOfDay(), false)) }}).</span>
                                </div>
                            @endif
                        @enderror
                        @error('due_time')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Parent Task Relation</label>
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input wire:model.live="is_subtask" type="checkbox" class="w-3.5 h-3.5 rounded accent-secondary" />
                                <span class="font-label-default text-label-default text-on-surface-variant">Make Sub-task</span>
                            </label>
                        </div>
                        @if ($is_subtask)
                            <div class="relative">
                                <select wire:model="parent_id" class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm appearance-none outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm cursor-pointer pr-10">
                                    <option value="">— Select parent task —</option>
                                    @foreach ($parentTasks as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                    @endforeach
                                </select>
                                <x-rune-icon name="arrows-corner-down-right" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none w-[18px] h-[18px]" />
                            </div>
                            @error('parent_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                        @else
                            <p class="h-10 flex items-center font-body-sm text-body-sm text-outline">This task stands alone (not a sub-task).</p>
                        @endif
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Workload Estimate</label>
                        <div class="grid grid-cols-2 gap-space-sm">
                            <div class="relative">
                                <input wire:model="story_points" type="number" min="0" max="100" placeholder="Story Points"
                                    class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm shadow-sm outline-none pr-16" />
                                <span class="font-label-uppercase text-label-uppercase text-outline absolute right-3 top-1/2 -translate-y-1/2">Points</span>
                            </div>
                            <div class="relative">
                                <input wire:model="estimate_hours" type="number" min="0" max="10000" placeholder="Work Hours"
                                    class="w-full h-10 px-space-md bg-surface-container-lowest text-on-surface rounded-xl font-body-sm text-body-sm shadow-sm outline-none pr-16" />
                                <span class="font-label-uppercase text-label-uppercase text-outline absolute right-3 top-1/2 -translate-y-1/2">Est. Hours</span>
                            </div>
                        </div>
                        @error('story_points')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                        @error('estimate_hours')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 flex flex-col gap-space-xs pt-space-xs">
                        <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider" for="tagInput">Label &amp; Tag Classification</label>
                        <div class="flex flex-wrap items-center gap-space-xs p-space-sm bg-surface-container-lowest rounded-xl shadow-sm">
                            @foreach ($tags as $index => $tag)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container-high text-primary font-label-default text-label-default font-medium">
                                    #{{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $index }})" class="hover:text-error">
                                        <x-rune-icon name="indicators-x" class="w-[14px] h-[14px]" />
                                    </button>
                                </span>
                            @endforeach
                            <input wire:model="tagInput" wire:keydown.enter.prevent="addTag" id="tagInput" type="text" placeholder="Type a tag then Enter..."
                                class="flex-1 min-w-40 bg-transparent outline-none font-body-sm text-body-sm text-on-surface placeholder:text-outline" />
                        </div>
                        @error('tags')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Attachments --}}
                <div class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <label class="font-title-sm text-title-sm text-on-surface">Attachment &amp; Supporting Files
                            <span class="font-code-sm text-code-sm text-outline font-normal">({{ count($attachments) }} selected)</span>
                        </label>
                        <span class="font-label-default text-label-default text-outline">PDF, DOC, PNG, JPG, ZIP max. 25 MB</span>
                    </div>
                    <label class="relative border-2 border-dashed border-outline-variant/60 rounded-xl p-space-md flex flex-col items-center justify-center gap-space-xs hover:bg-surface-container-low transition-colors group cursor-pointer">
                        <input wire:model="attachments" type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer" />
                        <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-secondary group-hover:scale-105 transition-transform">
                            <x-rune-icon name="nature-cloud-upload" class="w-5 h-5" />
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface font-medium text-center">
                            Drag &amp; drop files here, or <span class="text-secondary underline">browse local files</span>
                        </p>
                        <p class="font-code-sm text-code-sm text-outline">Supports multi-file (max. 5)</p>
                    </label>
                    @error('attachments')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    @error('attachments.*')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                    @foreach ($attachments as $index => $upload)
                        <div class="flex items-center justify-between p-space-sm px-space-md bg-surface-container-low rounded-xl shadow-sm">
                            <div class="flex items-center gap-space-md min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary flex items-center justify-center shrink-0">
                                    <x-rune-icon name="documents-file-text" class="w-[18px] h-[18px]" />
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-body-medium text-body-medium font-semibold text-on-surface truncate">{{ $upload->getClientOriginalName() }}</span>
                                    <span class="font-code-sm text-code-sm text-outline">{{ number_format($upload->getSize() / 1024, 0) }} KB • Ready to upload</span>
                                </div>
                            </div>
                            <button type="button" wire:click="removeAttachment({{ $index }})" title="Delete file" class="p-1.5 text-on-surface-variant hover:text-error rounded-lg hover:bg-error-container transition-colors">
                                <x-rune-icon name="tools-trash" class="w-[18px] h-[18px]" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-space-xl py-space-md bg-surface-container-low flex items-center justify-between gap-space-md">
                <a href="{{ route('tasks.mine') }}" class="h-10 px-space-md inline-flex items-center bg-surface-container-lowest text-on-surface font-body-medium text-body-medium rounded-xl hover:bg-surface-container transition-colors shadow-sm">
                    Cancel
                </a>
                <div class="flex items-center gap-space-lg">
                    <label class="hidden sm:flex items-center gap-space-xs cursor-pointer select-none">
                        <input wire:model="createAnother" type="checkbox" class="w-4 h-4 rounded accent-secondary cursor-pointer" />
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Create another task after this</span>
                    </label>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 px-space-xl bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-all shadow-md flex items-center gap-space-xs active:scale-[0.98] disabled:opacity-60">
                        <x-rune-icon name="indicators-plus" class="w-[18px] h-[18px]" wire:loading.remove />
                        <span wire:loading>Saving...</span>
                        <span wire:loading.remove>Create Task</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
