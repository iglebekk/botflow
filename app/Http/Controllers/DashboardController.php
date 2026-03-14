<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = request()->user();

        $tasks = Task::query()
            ->forUserTenant($user)
            ->with(['creator', 'assignee', 'subtasks'])
            ->latest()
            ->get();

        $columns = collect(TaskStatus::cases())
            ->map(function (TaskStatus $status) use ($tasks): array {
                $statusTasks = $tasks
                    ->filter(fn (Task $task) => $task->status === $status)
                    ->values();

                return [
                    'key' => $status->value,
                    'label' => __('tasks.status.'.$status->value),
                    'description' => __('tasks.dashboard.descriptions.'.$status->value),
                    'count' => $statusTasks->count(),
                    'tasks' => $statusTasks,
                ];
            });

        return view('dashboard', [
            'columns' => $columns,
            'taskCount' => $tasks->count(),
        ]);
    }
}
