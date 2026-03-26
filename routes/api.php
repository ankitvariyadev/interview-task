<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SubtaskController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['web', 'auth'])
    ->group(function (): void {
        Route::get('users', [UserController::class, 'index'])->name('api.users.index');
        Route::get('subtasks', [SubtaskController::class, 'index'])->name('api.subtasks.index');
        Route::post('tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('api.tasks.subtasks.store');
        Route::apiResource('tasks', TaskController::class)->names('api.tasks');
    });
