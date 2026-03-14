<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCommentRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Task $task): RedirectResponse
    {
        $task->comments()->create([
            'body' => $request->string('body')->value(),
            'is_delivery' => $request->boolean('is_delivery'),
            'author_type' => $request->user()::class,
            'author_id' => $request->user()->getKey(),
        ]);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', __('tasks.messages.comment_added'));
    }
}
