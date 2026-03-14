<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskSubtaskController extends Controller
{
    public function store(StoreTaskRequest $request, Task $task): JsonResponse
    {
        $subtask = Task::query()->create([
            'parent_task_id' => $task->id,
            'title' => $request->string('title')->value(),
            'description' => $request->input('description'),
            'priority' => $request->enum('priority', \App\Enums\TaskPriority::class),
            'requested_start_at' => $request->date('requested_start_at'),
            'status' => \App\Enums\TaskStatus::Open,
            'creator_type' => $request->user()::class,
            'creator_id' => $request->user()->getKey(),
            'assignee_type' => $request->assignee()::class,
            'assignee_id' => $request->assignee()->getKey(),
        ]);

        $subtask->load(['creator', 'assignee', 'subtasks', 'comments.author']);

        return TaskResource::make($subtask)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
