@extends('layouts.app')

@section('content')
    <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
            <article class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)] sm:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Task Detail</p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]
                                {{ $task->status->value === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($task->status->value === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                {{ $task->status->label() }}
                            </span>
                        </div>

                        <h1 class="font-[family:var(--font-display)] text-4xl font-semibold text-slate-950">{{ $task->title }}</h1>

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
                        @if ($isAdmin)
                            <a href="{{ route('tasks.edit', $task) }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Edit Task
                            </a>
                        @endif

                        <a href="{{ route('tasks.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                            Back
                        </a>
                    </div>
                </div>
            </article>

            <section class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)] sm:p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Subtasks</p>
                        <h2 class="mt-2 font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">Work breakdown</h2>
                    </div>
                </div>

                <div class="mt-6 grid gap-4">
                    @forelse ($task->subtasks as $subtask)
                        @include('tasks.partials.subtask-tree', ['subtask' => $subtask, 'depth' => 0])
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                            No subtasks yet for this task.
                        </div>
                    @endforelse
                </div>
            </section>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)]">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Update Status</p>
                <h2 class="mt-2 font-[family:var(--font-display)] text-2xl font-semibold text-slate-950">Move the task forward</h2>

                <form method="POST" action="{{ route('api.tasks.update', $task) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($task->status->value === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Save Status
                    </button>
                </form>

                @if ($task->status->value !== 'completed')
                    <form method="POST" action="{{ route('api.tasks.update', $task) }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                            Mark Completed
                        </button>
                    </form>
                @endif
            </section>

            @if (auth()->user()->can('create', [\App\Models\Subtask::class, $task]))
                <section class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Add Subtask</p>
                    <h2 class="mt-2 font-[family:var(--font-display)] text-2xl font-semibold text-slate-950">Create the next step</h2>

                    <form method="POST" action="{{ route('api.tasks.subtasks.store', $task) }}" class="mt-6 space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label for="subtask_title" class="text-sm font-semibold text-slate-700">Title</label>
                            <input
                                id="subtask_title"
                                name="title"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="subtask_description" class="text-sm font-semibold text-slate-700">Description</label>
                            <textarea
                                id="subtask_description"
                                name="description"
                                rows="4"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400"
                                placeholder="Optional context for the subtask"
                            ></textarea>
                        </div>

                        @if ($subtaskOptions->isNotEmpty())
                            <div class="space-y-2">
                                <label for="subtask_parent_subtask_id" class="text-sm font-semibold text-slate-700">Parent subtask</label>
                                <select
                                    id="subtask_parent_subtask_id"
                                    name="parent_subtask_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400"
                                >
                                    <option value="">Add directly under task</option>
                                    @foreach ($subtaskOptions as $option)
                                        <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Create Subtask
                        </button>
                    </form>
                </section>
            @endif

            @if ($isAdmin)
                <section class="rounded-[2rem] border border-rose-200/70 bg-rose-50/80 p-6 shadow-[0_30px_80px_-45px_rgba(127,29,29,0.15)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Danger Zone</p>
                    <h2 class="mt-2 font-[family:var(--font-display)] text-2xl font-semibold text-rose-900">Delete task</h2>
                    <p class="mt-2 text-sm leading-7 text-rose-700">Removing a task also removes every subtask attached to it.</p>

                    <form method="POST" action="{{ route('api.tasks.destroy', $task) }}" class="mt-5">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-2xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                            Delete Task
                        </button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
@endsection
