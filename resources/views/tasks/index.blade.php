@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="flex flex-col gap-6 rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.4)] backdrop-blur sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-500">{{ $isAdmin ? 'Admin Console' : 'Assigned Work' }}</p>
                    <h1 class="font-[family:var(--font-display)] text-4xl font-semibold text-slate-950">
                        {{ $isAdmin ? 'Manage users, tasks, and subtasks' : 'Track your assigned tasks' }}
                    </h1>
                    <p class="max-w-2xl text-sm leading-7 text-slate-600">
                        {{ $isAdmin ? 'Create and assign tasks, monitor progress across every user, and review all subtasks from one dashboard.' : 'Search your assignments, filter by status, update progress, and drill into subtasks from the same workspace.' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($isAdmin)
                        <a href="{{ route('tasks.create') }}" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Create Task
                        </a>
                        <a href="{{ route('subtasks.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                            View Subtasks
                        </a>
                    @endif

                    <a href="{{ route('api.tasks.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                        API Tasks
                    </a>
                </div>
            </div>

            @if ($isAdmin && $dashboardStats)
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5">
                        <p class="text-sm font-semibold text-slate-500">Users</p>
                        <p class="mt-3 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">{{ $dashboardStats['users'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5">
                        <p class="text-sm font-semibold text-slate-500">Tasks</p>
                        <p class="mt-3 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">{{ $dashboardStats['tasks'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5">
                        <p class="text-sm font-semibold text-slate-500">Subtasks</p>
                        <p class="mt-3 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">{{ $dashboardStats['subtasks'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5">
                        <p class="text-sm font-semibold text-slate-500">Completed</p>
                        <p class="mt-3 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">{{ $dashboardStats['completed_tasks'] }}</p>
                    </article>
                </div>
            @endif

            <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-4 rounded-3xl border border-slate-200/70 bg-slate-50/70 p-4 md:grid-cols-[1fr_220px_auto]">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search tasks by title or description"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400"
                >

                <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-3">
                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>
                </div>
            </form>
        </section>

        @if ($isAdmin)
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Users</p>
                        <h2 class="mt-2 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">Assigned team members</h2>
                    </div>
                    <a href="{{ route('api.users.index') }}" class="text-sm font-semibold text-slate-600 underline-offset-4 hover:underline">Users API</a>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($users as $user)
                        <article class="rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_60px_-45px_rgba(15,23,42,0.5)]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">User</span>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Tasks</p>
                                    <p class="mt-2 text-2xl font-[family:var(--font-display)] font-semibold text-slate-900">{{ $user->tasks_count }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Completed</p>
                                    <p class="mt-2 text-2xl font-[family:var(--font-display)] font-semibold text-slate-900">{{ $user->completed_tasks_count }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tasks</p>
                <h2 class="mt-2 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">
                    {{ $isAdmin ? 'All tracked tasks' : 'Your current assignments' }}
                </h2>
            </div>

            <div class="grid gap-4">
                @forelse ($tasks as $task)
                    <article class="rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-[0_18px_48px_-42px_rgba(15,23,42,0.55)]">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-xl font-semibold text-slate-950">{{ $task->title }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]
                                        {{ $task->status->value === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($task->status->value === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $task->status->label() }}
                                    </span>
                                </div>

                                @if ($task->description)
                                    <p class="max-w-3xl text-sm leading-7 text-slate-600">{{ $task->description }}</p>
                                @endif

                                <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                                    <span>Assigned to {{ $task->assignedUser->name }}</span>
                                    <span>{{ $task->subtasks_count }} subtasks</span>
                                    <span>Updated {{ $task->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('tasks.show', $task) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    View
                                </a>

                                @if ($isAdmin)
                                    <a href="{{ route('tasks.edit', $task) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white/70 p-10 text-center text-sm text-slate-500">
                        No tasks matched the current search or status filter.
                    </div>
                @endforelse
            </div>

            {{ $tasks->links() }}
        </section>
    </div>
@endsection
