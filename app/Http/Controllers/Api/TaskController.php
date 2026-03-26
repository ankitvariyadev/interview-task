<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->with('assignedUser')
            ->withCount('subtasks')
            ->visibleTo($request->user())
            ->search($request->string('search')->toString())
            ->filterStatus(TaskStatus::tryFrom($request->string('status')->toString()))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse|RedirectResponse
    {
        $payload = $request->validated();
        $payload['status'] ??= TaskStatus::Pending->value;

        $task = Task::query()->create($payload);
        $task->load('assignedUser')->loadCount('subtasks');

        return $this->taskResponse($request, $task, 'Task created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task): TaskResource
    {
        $this->authorize('view', $task);

        $task->load([
            'assignedUser',
            'subtasks' => fn ($query) => $query->latest('id'),
        ])->loadCount('subtasks');

        return TaskResource::make($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $task->update($request->validated());
        $task->refresh()->load('assignedUser')->loadCount('subtasks');

        return $this->taskResponse($request, $task, 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        if (! $request->expectsJson()) {
            return redirect()->route('tasks.index')->with('status', 'Task deleted successfully.');
        }

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    private function taskResponse(Request $request, Task $task, string $message, int $status = 200): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            return redirect()->route('tasks.show', $task)->with('status', $message);
        }

        return TaskResource::make($task)
            ->additional(['message' => $message])
            ->response()
            ->setStatusCode($status);
    }
}
