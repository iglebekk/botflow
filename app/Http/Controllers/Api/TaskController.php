<?php

namespace App\Http\Controllers\Api;

use App\Actions\UpdateTaskStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Bot;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function __construct(
        private UpdateTaskStatusAction $updateTaskStatus,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $bot = request()->user();

        abort_unless($bot instanceof Bot, Response::HTTP_FORBIDDEN);

        $tasks = Task::query()
            ->with(['creator', 'assignee', 'subtasks'])
            ->whereMorphedTo('assignee', $bot)
            ->latest()
            ->get();

        return TaskResource::collection($tasks);
    }

    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        $task->load([
            'creator',
            'assignee',
            'subtasks.creator',
            'subtasks.assignee',
            'subtasks.comments.author',
            'comments.author',
        ]);

        return TaskResource::make($task);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): TaskResource
    {
        $this->updateTaskStatus->execute($task, $request->status());

        $task->load(['creator', 'assignee', 'subtasks', 'comments.author']);

        return TaskResource::make($task);
    }
}
