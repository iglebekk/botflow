<?php

namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Validation\ValidationException;

class UpdateTaskStatusAction
{
    public function execute(Task $task, TaskStatus $status): Task
    {
        if ($status->isClosed() && ! $task->load('subtasks')->canBeClosed()) {
            throw ValidationException::withMessages([
                'status' => [__('tasks.validation.parent_requires_closed_subtasks')],
            ]);
        }

        $task->update([
            'status' => $status,
            'closed_at' => $status->isClosed() ? now() : null,
        ]);

        return $task;
    }
}
