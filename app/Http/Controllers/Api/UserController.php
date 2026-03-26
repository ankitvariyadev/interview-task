<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Role;
use App\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless($request->user()->hasRole(Role::Admin->value), 403);

        $users = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', Role::User->value))
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', TaskStatus::Completed->value),
            ])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return UserResource::collection($users);
    }
}
