<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\TaskStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SubtasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Subtask::class);

        $selectedStatus = TaskStatus::tryFrom($request->string('status')->toString());

        $subtasks = Subtask::query()
            ->with(['task.assignedUser:id,name,email'])
            ->search($request->string('search')->toString())
            ->filterStatus($selectedStatus)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('subtasks.index', [
            'search' => $request->string('search')->toString(),
            'selectedStatus' => $selectedStatus?->value,
            'statuses' => TaskStatus::cases(),
            'subtasks' => $subtasks,
        ]);
    }
}
