<?php

namespace App\Http\Controllers;

use App\Actions\UpdateTaskStatusAction;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskStatusController extends Controller
{
    public function __construct(
        private UpdateTaskStatusAction $updateTaskStatus,
    ) {}

    public function update(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $this->updateTaskStatus->execute($task, $request->status());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', __('tasks.messages.status_updated'));
    }
}
