<?php

use App\Http\Controllers\BotController;
use App\Http\Controllers\BotApiDocumentationController;
use App\Http\Controllers\BotTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskSubtaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('tasks', TaskController::class)->except(['destroy']);
    Route::patch('tasks/{task}/status', [TaskStatusController::class, 'update'])->name('tasks.status.update');
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::post('tasks/{task}/subtasks', [TaskSubtaskController::class, 'store'])->name('tasks.subtasks.store');

    Route::resource('bots', BotController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('bots/{bot}/tokens', [BotTokenController::class, 'store'])->name('bots.tokens.store');
    Route::get('docs/bot-api', BotApiDocumentationController::class)->name('docs.bot-api');
});

require __DIR__.'/auth.php';
