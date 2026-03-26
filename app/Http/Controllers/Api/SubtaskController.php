<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Resources\SubtaskResource;
use App\Models\Subtask;
use App\Models\Task;
use App\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubtaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Subtask::class);

        $subtasks = Subtask::query()
            ->with(['task', 'task.assignedUser'])
            ->search($request->string('search')->toString())
            ->filterStatus(TaskStatus::tryFrom($request->string('status')->toString()))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return SubtaskResource::collection($subtasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubtaskRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $payload = $request->validated();
        $payload['status'] ??= TaskStatus::Pending->value;

        $subtask = $task->subtasks()->create($payload);
        $subtask->load(['task', 'task.assignedUser']);

        if (! $request->expectsJson()) {
            return redirect()->route('tasks.show', $task)->with('status', 'Subtask created successfully.');
        }

        return SubtaskResource::make($subtask)
            ->additional(['message' => 'Subtask created successfully.'])
            ->response()
            ->setStatusCode(201);
    }
}
