<?php

namespace App\Livewire;

use App\Models\AccessAuditLog;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AddTeamMember extends Component
{
    public bool $open = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $nip = '';

    public string $position = '';

    public string $campus = '';

    public string $work_unit_id = '';

    public string $role_name = 'team-member';

    #[On('open-add-member')]
    public function openModal(): void
    {
        $this->reset(['name', 'email', 'password', 'nip', 'position', 'campus', 'work_unit_id']);
        $this->role_name = 'team-member';
        $this->resetValidation();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->authorizeInvite();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nip' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'campus' => ['nullable', 'string', 'max:255'],
            'work_unit_id' => ['nullable', 'integer', 'exists:work_units,id'],
            'role_name' => ['required', 'string', 'exists:roles,name'],
        ]);

        if (! $this->canManageRoles() && $data['role_name'] === 'admin') {
            $this->addError('role_name', 'You are not allowed to assign the admin role.');

            return;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'nip' => $data['nip'] ?: null,
            'position' => $data['position'] ?: null,
            'campus' => $data['campus'] ?: null,
            'work_unit_id' => $data['work_unit_id'] ?: null,
            'status' => 'pending_2fa',
            'two_factor_confirmed_at' => null,
        ]);

        $user->assignRole($data['role_name']);

        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => auth()->id(),
            'event' => 'member_added',
            'description' => "{$user->name} added as {$data['role_name']}.",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', "\"{$user->name}\" added to the team.");

        $this->open = false;
        $this->dispatch('team-updated');
    }

    public function render(): View
    {
        return view('livewire.add-team-member', [
            'units' => WorkUnit::orderBy('name')->get(),
            'roles' => $this->assignableRoles(),
        ]);
    }

    /**
     * Roles the actor is allowed to assign when provisioning a new account.
     *
     * @return Collection<int, Role>
     */
    private function assignableRoles(): Collection
    {
        $roles = Role::orderBy('name')->get();

        if ($this->canManageRoles()) {
            return $roles;
        }

        return $roles->reject(fn (Role $role): bool => $role->name === 'admin')->values();
    }

    private function canManageRoles(): bool
    {
        return auth()->user()->can('role.manage') || auth()->user()->can('user.manage');
    }

    private function authorizeInvite(): void
    {
        abort_unless(
            auth()->user()->can('user.invite')
                || auth()->user()->can('user.manage')
                || auth()->user()->can('role.manage'),
            403
        );
    }
}
