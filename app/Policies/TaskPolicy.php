<?php

namespace App\Policies;

use App\Models\Bot;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User|Bot $actor): bool
    {
        return true;
    }

    public function view(User|Bot $actor, Task $task): bool
    {
        if ($actor instanceof User) {
            return $task->belongsToUserTenant($actor);
        }

        return $task->belongsToUserTenant($actor->owner)
            && ($task->assignee?->is($actor)
                || $task->creator?->is($actor));
    }

    public function create(User|Bot $actor): bool
    {
        return true;
    }

    public function update(User|Bot $actor, Task $task): bool
    {
        if ($actor instanceof User) {
            return $task->belongsToUserTenant($actor);
        }

        return $task->belongsToUserTenant($actor->owner)
            && $task->assignee?->is($actor);
    }

    public function comment(User|Bot $actor, Task $task): bool
    {
        return $this->update($actor, $task);
    }

    public function createSubtask(User|Bot $actor, Task $task): bool
    {
        return $this->update($actor, $task);
    }
}
