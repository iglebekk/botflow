<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_task_id',
        'title',
        'description',
        'status',
        'priority',
        'requested_start_at',
        'closed_at',
        'creator_type',
        'creator_id',
        'assignee_type',
        'assignee_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'requested_start_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_task_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function hasOpenSubtasks(): bool
    {
        return $this->subtasks->contains(fn (Task $task) => ! $task->status->isClosed());
    }

    public function canBeClosed(): bool
    {
        return ! $this->hasOpenSubtasks();
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function belongsToUserTenant(User $user): bool
    {
        $botIds = $user->bots()
            ->withTrashed()
            ->pluck('id')
            ->all();

        return ($this->creator_type === User::class && $this->creator_id === $user->id)
            || ($this->assignee_type === User::class && $this->assignee_id === $user->id)
            || ($this->creator_type === Bot::class && in_array($this->creator_id, $botIds, true))
            || ($this->assignee_type === Bot::class && in_array($this->assignee_id, $botIds, true));
    }

    public function scopeForUserTenant(Builder $query, User $user): Builder
    {
        $botIds = $user->bots()
            ->withTrashed()
            ->pluck('id')
            ->all();

        return $query->where(function (Builder $query) use ($user, $botIds): void {
            $query
                ->where(function (Builder $query) use ($user): void {
                    $query->where('creator_type', User::class)
                        ->where('creator_id', $user->id);
                })
                ->orWhere(function (Builder $query) use ($user): void {
                    $query->where('assignee_type', User::class)
                        ->where('assignee_id', $user->id);
                })
                ->orWhere(function (Builder $query) use ($botIds): void {
                    $query->where('creator_type', Bot::class)
                        ->whereIn('creator_id', $botIds ?: [-1]);
                })
                ->orWhere(function (Builder $query) use ($botIds): void {
                    $query->where('assignee_type', Bot::class)
                        ->whereIn('assignee_id', $botIds ?: [-1]);
                });
        });
    }
}
