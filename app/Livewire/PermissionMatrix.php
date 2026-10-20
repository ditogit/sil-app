<?php

namespace App\Livewire;

use App\Models\AccessAuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionMatrix extends Component
{
    public bool $open = false;

    /** @var array<string, array<int, string>> role name => permission names */
    public array $selected = [];

    #[On('open-permission-matrix')]
    public function openModal(): void
    {
        $this->authorizeManage();
        $this->seedFromDatabase();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->authorizeManage();

        $permissions = Permission::pluck('name')->all();

        foreach (Role::all() as $role) {
            $names = array_values(array_intersect($this->selected[$role->name] ?? [], $permissions));
            $role->syncPermissions($names);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AccessAuditLog::create([
            'user_id' => auth()->id(),
            'actor_id' => auth()->id(),
            'event' => 'permission_changed',
            'description' => 'Updated the role permission matrix.',
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Role permission matrix saved.');

        $this->open = false;
        $this->dispatch('team-updated');
    }

    public function render(): View
    {
        return view('livewire.permission-matrix', [
            'roles' => Role::orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    private function seedFromDatabase(): void
    {
        $this->selected = Role::with('permissions')->get()
            ->mapWithKeys(fn (Role $role) => [$role->name => $role->permissions->pluck('name')->all()])
            ->all();
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()->can('role.manage'), 403);
    }
}
