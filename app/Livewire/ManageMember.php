<?php

namespace App\Livewire;

use App\Models\AccessAuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class ManageMember extends Component
{
    public bool $open = false;

    public ?int $userId = null;

    public string $role_name = '';

    public string $status = 'active';

    public bool $two_factor = false;

    #[On('open-manage-member')]
    public function openModal(int $userId): void
    {
        $this->authorizeManage();

        $user = User::with('roles')->findOrFail($userId);

        $this->userId = $user->id;
        $this->role_name = $user->roles->first()?->name ?? '';
        $this->status = $user->status;
        $this->two_factor = $user->hasTwoFactor();
        $this->resetValidation();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->authorizeManage();

        $data = $this->validate([
            'role_name' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', 'in:active,pending_2fa,suspended'],
            'two_factor' => ['boolean'],
        ]);

        $user = User::findOrFail($this->userId);

        $previousRole = $user->roles->first()?->name;
        $user->syncRoles([$data['role_name']]);

        $user->update([
            'status' => $data['status'],
            'two_factor_confirmed_at' => $data['two_factor']
                ? ($user->two_factor_confirmed_at ?? now())
                : null,
        ]);

        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => auth()->id(),
            'event' => 'permission_changed',
            'description' => $previousRole !== $data['role_name']
                ? "Role for {$user->name} changed from {$previousRole} to {$data['role_name']}."
                : "Access settings updated for {$user->name}.",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', "Access for \"{$user->name}\" updated.");

        $this->open = false;
        $this->dispatch('team-updated');
    }

    public function render(): View
    {
        return view('livewire.manage-member', [
            'user' => $this->userId ? User::with(['roles', 'workUnit'])->find($this->userId) : null,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    private function authorizeManage(): void
    {
        abort_unless(
            auth()->user()->can('manage users') || auth()->user()->can('manage roles'),
            403
        );
    }
}
