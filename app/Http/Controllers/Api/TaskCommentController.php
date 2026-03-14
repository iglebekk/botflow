<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskCommentRequest;
use App\Http\Resources\TaskCommentResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse
    {
        $comment = $task->comments()->create([
            'body' => $request->string('body')->value(),
            'is_delivery' => $request->boolean('is_delivery'),
            'author_type' => $request->user()::class,
            'author_id' => $request->user()->getKey(),
        ]);

        $comment->load('author');

        return TaskCommentResource::make($comment)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
