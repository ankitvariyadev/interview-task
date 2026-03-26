<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Role;
use App\TaskStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $isAdmin = $request->user()->hasRole(Role::Admin->value);
        $selectedStatus = TaskStatus::tryFrom($request->string('status')->toString());

        $tasks = Task::query()
            ->with(['assignedUser:id,name,email'])
            ->withCount('subtasks')
            ->visibleTo($request->user())
            ->search($request->string('search')->toString())
            ->filterStatus($selectedStatus)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('tasks.index', [
            'dashboardStats' => $this->dashboardStats($isAdmin),
            'isAdmin' => $isAdmin,
            'search' => $request->string('search')->toString(),
            'selectedStatus' => $selectedStatus?->value,
            'statuses' => TaskStatus::cases(),
            'tasks' => $tasks,
            'users' => $isAdmin ? $this->usersForOverview() : collect(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        abort_unless($request->user()->hasRole(Role::Admin->value), 403);

        return view('tasks.create', [
            'statuses' => TaskStatus::cases(),
            'users' => $this->assignableUsers(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task): View
    {
        $this->authorize('view', $task);

        $task->load([
            'assignedUser:id,name,email',
            'subtasks' => fn ($query) => $query->latest('id'),
        ]);

        return view('tasks.show', [
            'isAdmin' => $request->user()->hasRole(Role::Admin->value),
            'statuses' => TaskStatus::cases(),
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task): View
    {
        abort_unless($request->user()->hasRole(Role::Admin->value), 403);

        return view('tasks.edit', [
            'statuses' => TaskStatus::cases(),
            'task' => $task->load('assignedUser:id,name,email'),
            'users' => $this->assignableUsers(),
        ]);
    }

    /**
     * @return array<string, int>|null
     */
    private function dashboardStats(bool $isAdmin): ?array
    {
        if (! $isAdmin) {
            return null;
        }

        return [
            'users' => User::query()->whereHas('roles', fn ($query) => $query->where('name', Role::User->value))->count(),
            'tasks' => Task::query()->count(),
            'subtasks' => Subtask::query()->count(),
            'completed_tasks' => Task::query()->where('status', TaskStatus::Completed->value)->count(),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    private function usersForOverview(): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', Role::User->value))
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', TaskStatus::Completed->value),
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    private function assignableUsers(): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', Role::User->value))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
