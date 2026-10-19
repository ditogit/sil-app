<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickCreateProject extends Component
{
    use AuthorizesRequests;

    public bool $open = false;

    public string $name = '';

    public string $description = '';

    public string $start_date = '';

    public string $end_date = '';

    /** @var array<int> */
    public array $member_ids = [];

    #[On('open-create-project')]
    public function openModal(): void
    {
        $this->reset(['name', 'description', 'end_date', 'member_ids']);
        $this->start_date = today()->toDateString();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): mixed
    {
        $this->authorize('create', Project::class);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'member_ids' => ['array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $project = Project::create([
            'created_by' => auth()->id(),
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?: null,
            'status' => 'active',
            'progress' => 0,
        ]);

        $project->members()->syncWithoutDetaching(array_unique([...array_map('intval', $data['member_ids'] ?? []), auth()->id()]));

        session()->flash('success', "Project \"{$project->name}\" created successfully.");

        return redirect()->route('projects.show', $project);
    }

    public function render(): View
    {
        return view('livewire.quick-create-project', [
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
