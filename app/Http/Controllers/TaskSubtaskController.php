<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskSubtaskController extends Controller
{
    public function store(StoreTaskRequest $request, Task $task): RedirectResponse
    {
        Task::query()->create([
            'parent_task_id' => $task->id,
            'title' => $request->string('title')->value(),
            'description' => $request->input('description'),
            'priority' => $request->enum('priority', TaskPriority::class),
            'requested_start_at' => $request->date('requested_start_at'),
            'status' => TaskStatus::Open,
            'creator_type' => $request->user()::class,
            'creator_id' => $request->user()->getKey(),
            'assignee_type' => $request->assignee()::class,
            'assignee_id' => $request->assignee()->getKey(),
        ]);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', __('tasks.messages.subtask_created'));
    }
}
