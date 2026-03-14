<?php

namespace App\Models;

use Database\Factories\BotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Bot extends Authenticatable
{
    /** @use HasFactory<BotFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_active',
        'last_seen_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdTasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'creator');
    }

    public function assignedTasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'assignee');
    }

    public function taskComments(): MorphMany
    {
        return $this->morphMany(TaskComment::class, 'author');
    }

    public function displayName(): string
    {
        if ($this->trashed()) {
            return __('bots.history.deleted_name', ['name' => $this->name]);
        }

        return $this->name;
    }
}
