<div class="px-space-xl py-space-lg max-w-[1600px] mx-auto w-full" x-data="{ copied: '' }">
    <div class="max-w-5xl mx-auto bg-surface-container-lowest rounded-xl shadow-2xl flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="shrink-0 px-space-xl py-space-md shadow-[0_1px_4px_rgba(0,0,0,0.04)] flex items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-sm min-w-0 flex-wrap">
                <div class="flex items-center gap-space-xs font-label-default text-label-default text-on-surface-variant">
                    <a href="{{ route('projects.index') }}" class="hover:text-secondary transition-colors">{{ $task->project->name }}</a>
                    <x-rune-icon name="arrows-chevron-right" class="w-[14px] h-[14px]" />
                    <span>TSK-{{ $task->id }}</span>
                </div>
                <div class="flex items-center gap-space-xs">
                    @php
                        $prioPill = match ($task->priority) {
                            'critical' => ['bg-error text-on-error', 'other-zap', 'Critical'],
                            'high' => ['bg-error-container text-on-error-container', null, 'High'],
                            'medium' => ['bg-surface-container text-on-surface-variant', null, 'Medium'],
                            default => ['bg-surface-container-low text-outline', null, 'Low'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg font-label-uppercase text-label-uppercase font-semibold {{ $prioPill[0] }}">
                        @if ($prioPill[1])<x-rune-icon name="{{ $prioPill[1] }}" class="w-[13px] h-[13px]" />@endif
                        {{ $prioPill[2] }}
                    </span>
                    @if ($isOverdue)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-error text-on-error font-label-uppercase text-label-uppercase shadow-sm">
                            <x-rune-icon name="schedule-clock-alert" class="w-[13px] h-[13px]" />
                            Overdue {{ $task->due_date->diffInDays(today()) }} days
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-space-xs shrink-0">
                <button type="button" title="Copy ID &amp; Title"
                    x-on:click="navigator.clipboard?.writeText('TSK-{{ $task->id }}: {{ $task->title }}'); copied = 'id'; setTimeout(() => copied = '', 2000)"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-label-default text-label-default transition-colors">
                    <x-rune-icon name="code-copy" class="w-4 h-4" />
                    <span class="hidden sm:inline" x-text="copied === 'id' ? 'Copied!' : 'Copy ID'"></span>
                </button>
                <button type="button" title="Share Link"
                    x-on:click="navigator.clipboard?.writeText(window.location.href); copied = 'link'; setTimeout(() => copied = '', 2000)"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-label-default text-label-default transition-colors">
                    <x-rune-icon name="tools-share-2" class="w-4 h-4" />
                    <span class="hidden sm:inline" x-text="copied === 'link' ? 'Copied!' : 'Share'"></span>
                </button>
                @if ($canDeleteTask)
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = ! open" type="button" title="More Options" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors">
                            <x-rune-icon name="layouts-grip-vertical" class="w-[18px] h-[18px]" />
                        </button>
                        <div x-show="open" x-cloak x-on:click.outside="open = false" class="absolute right-0 mt-1 w-40 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/40 overflow-hidden z-30">
                            <button wire:click="deleteTask" wire:confirm="Delete this task and its sub-tasks?" type="button" class="w-full text-left px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container/30">Delete Task</button>
                        </div>
                    </div>
                @endif
                <div class="w-px h-5 bg-surface-container-high mx-1"></div>
                <a href="{{ route('tasks.mine') }}" title="Close" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container transition-colors">
                    <x-rune-icon name="indicators-x" class="w-5 h-5" />
                </a>
            </div>
        </div>

        {{-- Stepper --}}
        <div class="shrink-0 px-space-xl py-space-sm bg-surface-container-low/70 flex flex-wrap items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="font-label-uppercase text-label-uppercase text-outline mr-1">Status Workflow:</span>
                @foreach (['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label)
                    @php $isCurrent = $task->status === $value; @endphp
                    @if ($canChangeStatus)
                        <button wire:click="setStatus('{{ $value }}')" type="button"
                            class="flex items-center gap-1 px-3 py-1 rounded-lg transition-all {{ $isCurrent ? 'bg-primary-container text-on-primary font-body-medium text-body-medium shadow-sm' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container text-body-sm font-medium' }}">
                            <span class="w-2 h-2 rounded-full {{ $isCurrent ? 'bg-secondary-fixed animate-pulse' : 'bg-outline-variant' }}"></span>
                            <span>{{ $label }}</span>
                        </button>
                    @else
                        <span class="flex items-center gap-1 px-3 py-1 rounded-lg text-body-sm {{ $isCurrent ? 'bg-primary-container text-on-primary font-medium' : 'bg-surface-container-lowest text-on-surface-variant' }}">
                            <span class="w-2 h-2 rounded-full {{ $isCurrent ? 'bg-secondary-fixed' : 'bg-outline-variant' }}"></span>
                            <span>{{ $label }}</span>
                        </span>
                    @endif
                    @if (! $loop->last)<x-rune-icon name="arrows-arrow-right" class="text-outline w-[14px] h-[14px]" />@endif
                @endforeach
            </div>
            <div class="flex items-center gap-1 text-on-surface-variant font-label-uppercase text-label-uppercase bg-surface-container px-2.5 py-1 rounded-full">
                <x-rune-icon name="identity-shield-check" class="text-secondary w-[14px] h-[14px]" />
                <span>{{ $canEdit ? 'You can edit' : 'Read only' }}</span>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-space-xl py-space-lg flex flex-col gap-space-xl">
            <div class="flex flex-col gap-space-sm">
                @if ($editing)
                    <input wire:model="title" type="text" maxlength="160"
                        class="w-full font-headline-lg text-headline-lg font-bold tracking-tight bg-surface-container-low rounded-lg px-space-sm py-1 outline-none focus:ring-2 focus:ring-secondary/25 @error('title') ring-2 ring-error @enderror" />
                    @error('title')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                @else
                    <div class="group relative flex items-start justify-between gap-space-md">
                        <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">{{ $task->title }}</h1>
                        @if ($canEdit)
                            <button wire:click="startEdit" type="button" title="Edit Title" class="opacity-0 group-hover:opacity-100 p-1 text-on-surface-variant hover:text-secondary transition-opacity shrink-0">
                                <x-rune-icon name="tools-pencil" class="w-[18px] h-[18px]" />
                            </button>
                        @endif
                    </div>
                @endif
                <div class="flex flex-wrap items-center gap-space-md text-on-surface-variant font-body-sm text-body-sm">
                    <span>Created on {{ $task->created_at->locale('en')->translatedFormat('d M Y, H:i') }} WIB</span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    <span>{{ $task->project->name }}</span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    @if ($isOverdue)
                        <span class="text-error font-medium flex items-center gap-1">
                            <x-rune-icon name="schedule-clock-alert" class="w-[15px] h-[15px]" />
                            Due: {{ $task->due_date->locale('en')->translatedFormat('d M Y') }}{{ $task->due_time ? ', '.$task->due_time->format('H:i') : '' }} WIB
                        </span>
                    @else
                        <span>Due: {{ $task->due_date->locale('en')->translatedFormat('d M Y') }}{{ $task->due_time ? ', '.$task->due_time->format('H:i') : '' }} WIB</span>
                    @endif
                </div>
                @if ($task->parent)
                    <a href="{{ route('tasks.show', $task->parent) }}" class="flex items-center gap-1 font-body-sm text-body-sm text-secondary hover:underline w-fit">
                        <x-rune-icon name="arrows-corner-down-right" class="w-4 h-4" />
                        Sub-task of: {{ $task->parent->title }}
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
                {{-- Left --}}
                <div class="lg:col-span-7 flex flex-col gap-space-xl min-w-0">
                    <div class="flex flex-col gap-space-sm bg-surface-container-low/40 p-space-md rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="font-label-uppercase text-label-uppercase text-outline font-semibold tracking-wider">Description &amp; Technical Specs</span>
                            @if ($canEdit && ! $editing)
                                <button wire:click="startEdit" type="button" class="text-secondary font-label-default text-label-default hover:underline flex items-center gap-1">
                                    <x-rune-icon name="documents-file-pen" class="w-[14px] h-[14px]" /> Edit
                                </button>
                            @endif
                        </div>
                        @if ($editing)
                            <textarea wire:model="description" rows="6" placeholder="Write specifications, acceptance criteria..."
                                class="w-full p-space-sm bg-surface-container-lowest rounded-lg font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25 resize-y @error('description') ring-2 ring-error @enderror"></textarea>
                            @error('description')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                        @else
                            <div class="text-on-surface font-body-default text-body-default leading-relaxed whitespace-pre-line">{{ $task->description ?: 'No description yet.' }}</div>
                        @endif
                    </div>

                    @if ($editing)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md bg-surface-container-low p-space-md rounded-xl">
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Priority</label>
                                <div class="grid grid-cols-4 gap-1 p-1 bg-surface-container rounded-xl">
                                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                        <button type="button" wire:click="$set('priority', '{{ $value }}')"
                                            class="py-1.5 rounded-lg text-label-default font-label-default transition-all {{ $priority === $value ? 'bg-surface-container-lowest font-semibold shadow-sm text-error' : 'text-on-surface-variant hover:bg-surface-container-lowest' }}">{{ $label }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Due date</label>
                                <div class="grid grid-cols-2 gap-space-xs">
                                    <input wire:model="due_date" type="date" class="h-10 px-space-sm bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                                    <input wire:model="due_time" type="time" class="h-10 px-space-sm bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                                </div>
                                @error('due_date')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Milestone</label>
                                <select wire:model="milestone_id" class="h-10 px-space-sm bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none cursor-pointer">
                                    <option value="">— No milestone —</option>
                                    @foreach ($milestones as $milestone)
                                        <option value="{{ $milestone->id }}">{{ $milestone->title }}</option>
                                    @endforeach
                                </select>
                                @error('milestone_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-uppercase text-label-uppercase text-outline uppercase tracking-wider">Assignee</label>
                                @if ($canAssign)
                                    <select wire:model="assignee_id" class="h-10 px-space-sm bg-surface-container-lowest rounded-xl font-body-sm text-body-sm shadow-sm outline-none cursor-pointer">
                                        <option value="">— Not yet assigned —</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="h-10 flex items-center font-body-sm text-body-sm text-on-surface">{{ $task->assignee?->name ?? 'Not yet assigned' }}</p>
                                @endif
                                @error('assignee_id')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="col-span-1 sm:col-span-2 flex items-center gap-space-sm">
                                <button wire:click="saveEdit" type="button" class="h-9 px-space-md bg-secondary text-on-secondary rounded-xl font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors">Save</button>
                                <button wire:click="$set('editing', false)" type="button" class="h-9 px-space-md bg-surface-container-lowest rounded-xl font-body-medium text-body-medium hover:bg-surface-container transition-colors">Cancel</button>
                            </div>
                        </div>
                    @endif

                    {{-- Sub-task --}}
                    <div class="flex flex-col gap-space-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-space-sm">
                                <x-rune-icon name="documents-clipboard-list" class="text-primary w-5 h-5" />
                                <h3 class="font-title-sm text-title-sm text-on-surface font-bold">Checklist Sub-Task</h3>
                                <span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-code-sm text-code-sm font-semibold">{{ $doneCount }} / {{ $subtasks->count() }} Completed ({{ $subProgress }}%)</span>
                            </div>
                            @if (auth()->user()->can('create tasks'))
                                <button wire:click="$set('showAddSubtask', true)" type="button" class="text-secondary font-label-default text-label-default hover:underline flex items-center gap-0.5">
                                    <x-rune-icon name="indicators-plus" class="w-4 h-4" /> Add Sub-task
                                </button>
                            @endif
                        </div>
                        @if ($subtasks->isNotEmpty())
                            <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                                <div class="h-full bg-secondary rounded-full transition-all duration-500" style="width: {{ $subProgress }}%"></div>
                            </div>
                        @endif
                        <div class="flex flex-col gap-2">
                            @forelse ($subtasks as $subtask)
                                <label class="flex items-start gap-space-sm p-space-sm rounded-xl {{ $subtask->status === 'done' ? 'bg-surface-container-low' : 'bg-surface-container-lowest shadow-sm' }} transition-colors {{ Gate::allows('updateStatus', $subtask) ? 'cursor-pointer hover:bg-surface-container-low/60' : '' }}">
                                    <input wire:click="toggleSubtask({{ $subtask->id }})" type="checkbox" @checked($subtask->status === 'done') @disabled(! Gate::allows('updateStatus', $subtask))
                                        class="mt-1 w-4 h-4 rounded accent-secondary cursor-pointer disabled:cursor-not-allowed" />
                                    <div class="flex-1 min-w-0">
                                        <p class="font-body-medium text-body-medium font-semibold {{ $subtask->status === 'done' ? 'text-on-surface-variant line-through' : 'text-on-surface' }}">{{ $subtask->title }}</p>
                                        <span class="font-label-default text-label-default text-outline flex items-center gap-1 mt-0.5">
                                            @if ($subtask->status === 'done')
                                                <x-rune-icon name="indicators-circle-check" class="w-[13px] h-[13px] text-on-tertiary-container" />
                                                Completed{{ $subtask->assignee ? ' by '.$subtask->assignee->name : '' }}
                                            @else
                                                {{ \App\Livewire\TaskDetail::statusLabel($subtask->status) }}{{ $subtask->assignee ? ' • '.$subtask->assignee->name : '' }}
                                            @endif
                                        </span>
                                    </div>
                                </label>
                            @empty
                                <p class="font-body-sm text-body-sm text-outline">No sub-tasks yet.</p>
                            @endforelse
                            @if ($showAddSubtask)
                                <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-sm ring-1 ring-secondary/30">
                                    <input wire:model="subtaskTitle" wire:keydown.enter="addSubtask" type="text" maxlength="160" placeholder="Sub-task title, press Enter to add..."
                                        class="flex-1 h-9 px-space-sm bg-surface-container-low rounded-lg font-body-sm text-body-sm outline-none focus:ring-2 focus:ring-secondary/25" />
                                    <button wire:click="addSubtask" type="button" class="h-9 px-space-md bg-secondary text-on-secondary rounded-lg font-body-medium text-body-medium font-semibold hover:bg-secondary-container transition-colors shrink-0">Add</button>
                                    <button wire:click="$set('showAddSubtask', false)" type="button" class="p-2 text-on-surface-variant hover:text-error transition-colors shrink-0">
                                        <x-rune-icon name="indicators-x" class="w-[18px] h-[18px]" />
                                    </button>
                                </div>
                                @error('subtaskTitle')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                            @endif
                        </div>
                    </div>

                    {{-- Attachments --}}
                    <div class="flex flex-col gap-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-title-sm text-title-sm text-on-surface font-bold flex items-center gap-1.5">
                                <x-rune-icon name="documents-paperclip" class="text-secondary w-5 h-5" />
                                Attachments &amp; Technical Documents ({{ $task->attachments->count() }})
                            </span>
                            <span class="font-label-default text-label-default text-outline">Max. 25 MB / file</span>
                        </div>
                        @if ($canUpload)
                            <label class="relative border-2 border-dashed border-outline-variant/60 rounded-xl p-space-sm flex items-center justify-center gap-space-xs hover:bg-surface-container-low transition-colors cursor-pointer">
                                <input wire:model="uploads" type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer" />
                                <x-rune-icon name="nature-cloud-upload" class="text-secondary w-5 h-5" />
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Click to upload a file <span class="text-secondary font-medium">or drag &amp; drop here</span></span>
                            </label>
                            @error('uploads')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                            @error('uploads.*')<p class="font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                            @forelse ($task->attachments as $attachment)
                                <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow group">
                                    <div class="w-10 h-10 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                                        <x-rune-icon name="documents-file-text" class="w-[22px] h-[22px]" />
                                    </div>
                                    <div class="flex-1 min-w-0 leading-tight">
                                        <p class="font-body-medium text-body-medium text-on-surface font-medium truncate group-hover:text-secondary" title="{{ $attachment->file_name }}">{{ $attachment->file_name }}</p>
                                        <span class="font-label-default text-label-default text-outline">{{ number_format($attachment->file_size / 1024, 0) }} KB • {{ $attachment->uploader?->name }}</span>
                                    </div>
                                    <a href="{{ route('attachments.download', [$task, $attachment]) }}" title="Download" class="p-1 text-on-surface-variant hover:text-secondary rounded transition-colors">
                                        <x-rune-icon name="documents-file-down" class="w-[18px] h-[18px]" />
                                    </a>
                                    @if (Gate::allows('delete', $attachment))
                                        <button wire:click="deleteAttachment({{ $attachment->id }})" wire:confirm="Delete this attachment?" type="button" title="Delete" class="p-1 text-on-surface-variant hover:text-error rounded transition-colors">
                                            <x-rune-icon name="tools-trash" class="w-[18px] h-[18px]" />
                                        </button>
                                    @endif
                                </div>
                            @empty
                                <p class="font-body-sm text-body-sm text-outline sm:col-span-2">No attachments yet.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Discussion --}}
                    <div class="flex flex-col gap-space-md pt-space-md border-t border-surface-container-high">
                        <div class="flex items-center justify-between border-b border-surface-container-high pb-2">
                            <div class="flex items-center gap-space-md">
                                <button wire:click="$set('tab', 'comments')" type="button"
                                    class="font-title-sm text-title-sm flex items-center gap-1.5 pb-2 -mb-2.5 {{ $tab === 'comments' ? 'text-secondary font-bold border-b-2 border-secondary' : 'text-on-surface-variant hover:text-on-surface font-medium transition-colors' }}">
                                    <x-rune-icon name="messaging-message-circle" class="w-[18px] h-[18px]" />
                                    <span>Comment</span>
                                    <span class="px-1.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed text-label-default font-semibold">{{ $comments->count() }}</span>
                                </button>
                                <button wire:click="$set('tab', 'activity')" type="button"
                                    class="font-title-sm text-title-sm flex items-center gap-1.5 pb-2 -mb-2.5 {{ $tab === 'activity' ? 'text-secondary font-bold border-b-2 border-secondary' : 'text-on-surface-variant hover:text-on-surface font-medium transition-colors' }}">
                                    <x-rune-icon name="schedule-history" class="w-[18px] h-[18px]" />
                                    <span>Activity History</span>
                                </button>
                            </div>
                        </div>

                        @if ($tab === 'comments')
                            <div class="flex flex-col gap-space-lg">
                                @forelse ($comments as $comment)
                                    <div class="flex items-start gap-space-md">
                                        <div class="w-8 h-8 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-default text-label-default font-bold shrink-0 mt-0.5">{{ $comment->author->initials() }}</div>
                                        <div class="flex-1 min-w-0 bg-surface-container-low/50 rounded-xl p-space-sm">
                                            <div class="flex items-center justify-between gap-space-sm">
                                                <p class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $comment->author->name }}
                                                    <span class="font-label-default text-label-default text-outline font-normal">• {{ $comment->created_at->locale('en')->diffForHumans() }}</span>
                                                </p>
                                                @if (Gate::allows('delete', $comment))
                                                    <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="Delete this comment?" type="button" title="Delete comment" class="text-outline hover:text-error transition-colors shrink-0">
                                                        <x-rune-icon name="tools-trash" class="w-4 h-4" />
                                                    </button>
                                                @endif
                                            </div>
                                            <p class="font-body-sm text-body-sm text-on-surface mt-1 whitespace-pre-line">{{ $comment->body }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="font-body-sm text-body-sm text-outline">No comments yet. Start the discussion below.</p>
                                @endforelse
                            </div>

                            @if ($canComment)
                                <div class="flex items-start gap-space-md" x-data="{ insertMd(ta, before, after = '') { const s = ta.selectionStart, e = ta.selectionEnd, v = ta.value; ta.value = v.slice(0, s) + before + v.slice(s, e) + after + v.slice(e); ta.focus(); ta.dispatchEvent(new Event('input')); } }">
                                    <div class="w-8 h-8 rounded-full bg-surface-container-high text-primary flex items-center justify-center font-label-default text-label-default font-bold shrink-0 mt-0.5">{{ auth()->user()->initials() }}</div>
                                    <div class="flex-1 rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden flex flex-col">
                                        <div class="flex items-center justify-between px-space-md py-1.5 bg-surface-container-low/40">
                                            <div class="flex items-center gap-1">
                                                <button type="button" title="Bold" x-on:click="insertMd($refs.cb, '**', '**')" class="p-1 rounded hover:bg-surface-container font-bold text-[15px] leading-none w-7 h-7 text-on-surface-variant hover:text-on-surface transition-colors">B</button>
                                                <button type="button" title="Italic" x-on:click="insertMd($refs.cb, '_', '_')" class="p-1 rounded hover:bg-surface-container italic text-[15px] leading-none w-7 h-7 text-on-surface-variant hover:text-on-surface transition-colors">I</button>
                                                <button type="button" title="Code Format" x-on:click="insertMd($refs.cb, '`', '`')" class="p-1 rounded hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors">
                                                    <x-rune-icon name="code-code" class="w-4 h-4" />
                                                </button>
                                                <button type="button" title="Bullet List" x-on:click="insertMd($refs.cb, '\n- ')" class="p-1 rounded hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors">
                                                    <x-rune-icon name="tools-list-filter" class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <span class="font-label-default text-label-default text-outline">Supports Markdown</span>
                                        </div>
                                        <textarea wire:model="commentBody" x-ref="cb" rows="3" placeholder="Write instructions, progress updates..."
                                            x-on:keydown.ctrl.enter="$wire.sendComment()"
                                            class="w-full p-space-md font-body-sm text-body-sm text-on-surface placeholder:text-outline bg-transparent outline-none resize-none @error('commentBody') ring-2 ring-inset ring-error @enderror"></textarea>
                                        <div class="flex items-center justify-between px-space-md py-2 border-t border-surface-container-low">
                                            <span class="font-label-default text-label-default text-outline hidden sm:inline">Ctrl + Enter to send</span>
                                            <span></span>
                                            <button wire:click="sendComment" type="button" class="flex items-center gap-1 px-space-md py-1.5 rounded-lg bg-secondary text-on-secondary font-body-medium text-body-medium hover:bg-secondary-container transition-colors shadow-sm">
                                                <span>Send Comment</span>
                                                <x-rune-icon name="messaging-send" class="w-4 h-4" />
                                            </button>
                                        </div>
                                        @error('commentBody')<p class="px-space-md pb-2 font-body-sm text-body-sm text-error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col gap-space-md relative before:absolute before:top-2 before:bottom-2 before:left-3.5 before:w-0.5 before:bg-surface-container">
                                <div class="flex items-start gap-space-md relative">
                                    <div class="w-7 h-7 rounded-full bg-surface-container-high text-primary flex items-center justify-center shrink-0 z-10">
                                        <x-rune-icon name="indicators-plus" class="w-[14px] h-[14px]" />
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="font-body-sm text-body-sm text-on-surface"><strong class="font-semibold">{{ $task->creator?->name }}</strong> created this task</p>
                                        <span class="font-label-default text-label-default text-outline mt-1">{{ $task->created_at->locale('en')->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @foreach ($comments as $comment)
                                    <div class="flex items-start gap-space-md relative">
                                        <div class="w-7 h-7 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant flex items-center justify-center shrink-0 z-10">
                                            <x-rune-icon name="messaging-message-circle" class="w-[14px] h-[14px]" />
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="font-body-sm text-body-sm text-on-surface"><strong class="font-semibold">{{ $comment->author->name }}</strong> commented</p>
                                            <span class="font-label-default text-label-default text-outline mt-1">{{ $comment->created_at->locale('en')->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                @if ($task->status === 'done')
                                    <div class="flex items-start gap-space-md relative">
                                        <div class="w-7 h-7 rounded-full bg-tertiary-fixed/40 text-on-tertiary-fixed-variant flex items-center justify-center shrink-0 z-10">
                                            <x-rune-icon name="indicators-circle-check" class="w-[14px] h-[14px]" />
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="font-body-sm text-body-sm text-on-surface">Task completed</p>
                                            <span class="font-label-default text-label-default text-outline mt-1">{{ $task->completed_at?->locale('en')->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: metadata --}}
                <div class="lg:col-span-5 flex flex-col gap-space-md bg-surface-container-low p-space-md rounded-xl lg:sticky lg:top-20">
                    <div class="flex flex-col gap-space-sm">
                        <div class="flex items-center justify-between font-body-sm text-body-sm">
                            <span class="text-on-surface-variant">Status</span>
                            <span class="font-semibold text-secondary">{{ \App\Livewire\TaskDetail::statusLabel($task->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between font-body-sm text-body-sm">
                            <span class="text-on-surface-variant">Assignee</span>
                            <span class="flex items-center gap-1.5 font-medium text-on-surface">
                                @if ($task->assignee)
                                    <span class="w-5 h-5 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center text-[10px] font-bold">{{ $task->assignee->initials() }}</span>
                                    {{ $task->assignee->name }}
                                @else
                                    <span class="text-outline">Not yet assigned</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between font-body-sm text-body-sm">
                            <span class="text-on-surface-variant">Created by</span>
                            <span class="font-medium text-on-surface">{{ $task->creator?->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between font-body-sm text-body-sm">
                            <span class="text-on-surface-variant">Due date</span>
                            <span class="font-medium {{ $isOverdue ? 'text-error' : 'text-on-surface' }}">{{ $task->due_date->locale('en')->translatedFormat('d M Y') }}{{ $task->due_time ? ' • '.$task->due_time->format('H:i') : '' }}</span>
                        </div>
                        <div class="flex items-center justify-between font-body-sm text-body-sm">
                            <span class="text-on-surface-variant">Milestone</span>
                            <span class="font-medium text-on-surface">{{ $task->milestone?->title ?? '—' }}</span>
                        </div>
                        @if ($task->story_points || $task->estimate_hours)
                            <div class="flex items-center justify-between font-body-sm text-body-sm">
                                <span class="text-on-surface-variant">Estimate</span>
                                <span class="font-medium text-on-surface">{{ $task->story_points ? $task->story_points.' SP' : '' }}{{ $task->story_points && $task->estimate_hours ? ' • ' : '' }}{{ $task->estimate_hours ? $task->estimate_hours.' h' : '' }}</span>
                            </div>
                        @endif
                    </div>
                    @if ($task->tags)
                        <div class="flex flex-col gap-1.5 pt-2 border-t border-surface-container">
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Label / Category:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($task->tags as $tag)
                                    <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface font-label-default text-label-default">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-space-xl py-space-md shadow-[0_-2px_10px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm">
                <x-rune-icon name="schedule-refresh-cw" class="text-secondary w-4 h-4" />
                <span>All changes are saved automatically</span>
            </div>
            <a href="{{ route('tasks.mine') }}" class="px-space-md py-2 rounded-xl bg-surface-container-low text-on-surface hover:bg-surface-container font-body-medium text-body-medium transition-colors">Close</a>
        </div>
    </div>
</div>
