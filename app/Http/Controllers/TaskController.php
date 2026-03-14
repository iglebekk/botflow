<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Bot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::query()
            ->forUserTenant(request()->user())
            ->with(['creator', 'assignee', 'parentTask', 'subtasks'])
            ->latest()
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'assigneeOptions' => $this->assigneeOptions(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Task::query()->create([
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
            ->with('success', __('tasks.messages.created'));
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task->load([
            'creator',
            'assignee',
            'parentTask',
            'subtasks.creator',
            'subtasks.assignee',
            'comments.author',
        ]);

        return view('tasks.show', [
            'task' => $task,
            'assigneeOptions' => $this->assigneeOptions(),
        ]);
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $task->load(['creator', 'assignee']);

        return view('tasks.edit', [
            'task' => $task,
            'assigneeOptions' => $this->assigneeOptions(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update([
            'title' => $request->string('title')->value(),
            'description' => $request->input('description'),
            'priority' => $request->enum('priority', TaskPriority::class),
            'requested_start_at' => $request->date('requested_start_at'),
            'assignee_type' => $request->assignee()::class,
            'assignee_id' => $request->assignee()->getKey(),
        ]);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', __('tasks.messages.updated'));
    }

    /**
     * @return Collection<int, array{label: string, value: string}>
     */
    private function assigneeOptions(): Collection
    {
        /** @var User $user */
        $user = request()->user();

        $users = collect([[
            'label' => __('tasks.assignees.user_option', ['name' => $user->name]),
            'value' => "user:{$user->id}",
        ]]);

        $bots = $user->bots()
            ->orderBy('name')
            ->get()
            ->map(fn (Bot $bot) => [
                'label' => __('tasks.assignees.bot_option', ['name' => $bot->name]),
                'value' => "bot:{$bot->id}",
            ]);

        return $users->concat($bots)->values();
    }
}
