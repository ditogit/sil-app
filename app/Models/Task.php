<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['project_id', 'milestone_id', 'parent_id', 'assignee_id', 'created_by', 'title', 'description', 'status', 'priority', 'due_date', 'due_time', 'completed_at', 'tags', 'story_points', 'estimate_hours'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory, SoftDeletes;

    /** @var array<string, string> */
    protected $attributes = [
        'status' => 'todo',
        'priority' => 'medium',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'due_time' => 'datetime:H:i',
            'completed_at' => 'datetime',
            'tags' => 'array',
            'story_points' => 'integer',
            'estimate_hours' => 'integer',
        ];
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<Milestone, $this> */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    /** @return BelongsTo<Task, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /** @return HasMany<Task> */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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

    #[Scope]
    protected function overdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', today())->where('status', '!=', 'done');
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->where('status', '!=', 'done');
    }

    #[Scope]
    protected function highPriority(Builder $query): Builder
    {
        return $query->where('priority', 'high');
    }
}
