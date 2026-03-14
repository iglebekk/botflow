<?php

use App\Http\Controllers\Api\TaskCommentController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskSubtaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('api.tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('api.tasks.show');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('api.tasks.status.update');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('api.tasks.comments.store');
    Route::post('/tasks/{task}/subtasks', [TaskSubtaskController::class, 'store'])->name('api.tasks.subtasks.store');
});
