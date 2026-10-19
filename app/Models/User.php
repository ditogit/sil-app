<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name', 'email', 'password', 'nip', 'position', 'work_unit_id',
    'campus', 'avatar_url', 'status', 'two_factor_confirmed_at', 'last_login_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * RBAC role metadata keyed by Spatie role name.
     *
     * @var array<string, array{label: string, level: int, badge: string, icon: string}>
     */
    public const ROLE_META = [
        'admin' => ['label' => 'Superadmin', 'level' => 0, 'badge' => 'bg-primary-container text-on-primary', 'icon' => 'shield-check'],
        'project_manager' => ['label' => 'Lead Developer / PM', 'level' => 1, 'badge' => 'bg-secondary-fixed text-on-secondary-fixed', 'icon' => 'code-bracket'],
        'team_member' => ['label' => 'Frontend & QA Engineer', 'level' => 2, 'badge' => 'bg-secondary-fixed-dim text-on-secondary-fixed-variant', 'icon' => 'globe-alt'],
        'viewer' => ['label' => 'Faculty PIC / Viewer', 'level' => 3, 'badge' => 'bg-surface-container-high text-on-surface', 'icon' => 'eye'],
        'none' => ['label' => 'No Role', 'level' => 99, 'badge' => 'bg-surface-variant text-on-surface-variant', 'icon' => 'user'],
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('view dashboard');
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name));

        return strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1] ?? '', 0, 1));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /** @return HasMany<Project> */
    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /** @return BelongsToMany<User> */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    /** @return HasMany<Task> */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    /** @return HasMany<Task> */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /** @return HasMany<Comment> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** @return HasMany<Attachment> */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /** @return BelongsTo<WorkUnit, $this> */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /** @return HasMany<AccessAuditLog> */
    public function accessAuditLogs(): HasMany
    {
        return $this->hasMany(AccessAuditLog::class);
    }

    public function hasTwoFactor(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * RBAC metadata for the user's primary role.
     *
     * @return array{name: string, label: string, level: int, badge: string, icon: string}
     */
    public function roleMeta(): array
    {
        $name = $this->roles->first()?->name;

        return array_merge(['name' => $name ?? 'none'], self::ROLE_META[$name] ?? self::ROLE_META['none']);
    }

    public function roleLevel(): int
    {
        return $this->roleMeta()['level'];
    }

    /**
     * Display status combining access status and 2FA enforcement.
     *
     * @return array{label: string, tone: string}
     */
    public function securityStatus(): array
    {
        if ($this->isSuspended()) {
            return ['label' => 'Suspended', 'tone' => 'error'];
        }

        if ($this->status === 'pending_2fa' || ! $this->hasTwoFactor()) {
            return ['label' => 'Pending 2FA', 'tone' => 'warning'];
        }

        return ['label' => '2FA Active', 'tone' => 'ok'];
    }
}
