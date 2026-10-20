<?php

namespace App\Livewire;

use App\Models\AccessAuditLog;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app', ['title' => 'Team Settings', 'breadcrumb' => 'Team Settings'])]
class TeamSettings extends Component
{
    use WithPagination;

    public string $tab = 'members';

    public string $q = '';

    public string $role = 'all';

    public string $unit = 'all';

    public string $status = 'all';

    public int $perPage = 10;

    /**
     * Permission matrix columns: screen label mapped to a Spatie permission.
     *
     * @var array<int, array{label: string, permission: string}>
     */
    public const MATRIX_COLUMNS = [
        ['label' => 'Create Project', 'permission' => 'project.create'],
        ['label' => 'Manage Members', 'permission' => 'project.manage-members'],
        ['label' => 'Assign Task', 'permission' => 'task.assign'],
        ['label' => 'Manage Roles', 'permission' => 'role.manage'],
        ['label' => 'Manage Users', 'permission' => 'user.manage'],
    ];

    public function updating(string $property): void
    {
        if (in_array($property, ['q', 'role', 'unit', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    #[On('team-updated')]
    public function refreshTeam(): void
    {
        // Re-render picks up changes made in the modal child components.
    }

    public function reviewPending(): void
    {
        $this->tab = 'members';
        $this->status = 'pending_2fa';
        $this->resetPage();
    }

    public function openAddMember(): void
    {
        $this->authorizeManage();

        $this->dispatch('open-add-member');
    }

    public function openManageMember(int $userId): void
    {
        $this->authorizeManage();

        $this->dispatch('open-manage-member', userId: $userId);
    }

    public function openMatrix(): void
    {
        $this->authorizeManage();

        $this->dispatch('open-permission-matrix');
    }

    public function approve(int $userId): void
    {
        $this->authorizeManage();

        $user = User::findOrFail($userId);
        $user->update(['status' => 'active', 'two_factor_confirmed_at' => now()]);

        $this->log($user, 'reactivated', "Access approved and 2FA activated for {$user->name}.");

        session()->flash('success', "\"{$user->name}\" access approved.");
    }

    public function suspend(int $userId): void
    {
        $this->authorizeManage();

        $user = User::findOrFail($userId);

        // Protect the last admin from being suspended.
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            session()->flash('error', 'Cannot suspend the last admin account.');

            return;
        }

        $user->update(['status' => 'suspended']);

        $this->log($user, 'suspended', "Access suspended for {$user->name}.");

        session()->flash('success', "\"{$user->name}\" suspended.");
    }

    public function reactivate(int $userId): void
    {
        $this->authorizeManage();

        $user = User::findOrFail($userId);
        $user->update(['status' => 'active']);

        $this->log($user, 'reactivated', "Access reactivated for {$user->name}.");

        session()->flash('success', "\"{$user->name}\" reactivated.");
    }

    public function render(): View
    {
        $canManage = $this->canManage();

        $totalAccounts = User::count();
        $twoFactorCount = User::whereNotNull('two_factor_confirmed_at')->count();

        return view('screens.pengaturan_tim', [
            'canManage' => $canManage,
            'members' => $this->members(),
            'pendingMembers' => User::where('status', 'pending_2fa')->orderBy('name')->get(),
            'roles' => Role::with('permissions')->withCount(['users'])->orderBy('name')->get(),
            'roleMeta' => User::ROLE_META,
            'units' => WorkUnit::withCount('users')->orderBy('name')->get(),
            'unitOptions' => WorkUnit::orderBy('name')->get(),
            'roleOptions' => Role::orderBy('name')->get(),
            'auditLogs' => AccessAuditLog::with(['user', 'actor'])->latest()->paginate($this->perPage, pageName: 'auditPage'),
            'lastSync' => AccessAuditLog::max('created_at'),
            'matrixColumns' => self::MATRIX_COLUMNS,
            'metrics' => [
                'total' => $totalAccounts,
                'ssoPercent' => $totalAccounts > 0 ? (int) round($twoFactorCount / $totalAccounts * 100) : 0,
                'superadmins' => User::role('admin')->count(),
                'pending' => User::where('status', 'pending_2fa')->count(),
            ],
        ]);
    }

    private function members(): LengthAwarePaginator
    {
        $term = '%'.mb_strtolower($this->q).'%';

        return User::query()
            ->with(['roles', 'workUnit', 'projects'])
            ->when($this->q !== '', function ($query) use ($term): void {
                $query->where(fn ($w) => $w
                    ->whereRaw('lower(name) like ?', [$term])
                    ->orWhereRaw('lower(email) like ?', [$term])
                    ->orWhereRaw('lower(nip) like ?', [$term])
                    ->orWhereRaw('lower(position) like ?', [$term]));
            })
            ->when($this->role !== 'all', fn ($query) => $query->whereHas('roles', fn ($r) => $r->where('name', $this->role)))
            ->when($this->unit !== 'all', fn ($query) => $query->where('work_unit_id', $this->unit))
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    private function canManage(): bool
    {
        return auth()->user()->can('user.manage') || auth()->user()->can('role.manage');
    }

    private function authorizeManage(): void
    {
        abort_unless($this->canManage(), 403);
    }

    private function log(User $subject, string $event, string $description): void
    {
        AccessAuditLog::create([
            'user_id' => $subject->id,
            'actor_id' => auth()->id(),
            'event' => $event,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
