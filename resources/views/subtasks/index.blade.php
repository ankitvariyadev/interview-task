@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Admin Console</p>
                    <h1 class="font-[family:var(--font-display)] text-4xl font-semibold text-slate-950">All subtasks</h1>
                    <p class="max-w-2xl text-sm leading-7 text-slate-600">Review the full subtask stream across every assigned task and user.</p>
                </div>

                <a href="{{ route('tasks.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                    Back to Tasks
                </a>
            </div>

            <form method="GET" action="{{ route('subtasks.index') }}" class="mt-6 grid gap-4 rounded-3xl border border-slate-200/70 bg-slate-50/70 p-4 md:grid-cols-[1fr_220px_auto]">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search subtasks"
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

                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Filter
                </button>
            </form>
        </section>

        <div class="grid gap-4">
            @forelse ($subtasks as $subtask)
                <article class="rounded-[1.75rem] border border-white/70 bg-white/80 p-5 shadow-[0_18px_48px_-42px_rgba(15,23,42,0.55)]">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-xl font-semibold text-slate-950">{{ $subtask->title }}</h2>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]
                                    {{ $subtask->status->value === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($subtask->status->value === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $subtask->status->label() }}
                                </span>
                            </div>

                            @if ($subtask->description)
                                <p class="text-sm leading-7 text-slate-600">{{ $subtask->description }}</p>
                            @endif

                            <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                                <span>Task: {{ $subtask->task->title }}</span>
                                <span>Assigned to {{ $subtask->task->assignedUser->name }}</span>
                                <span>Updated {{ $subtask->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <a href="{{ route('tasks.show', $subtask->task) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            View Task
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white/70 p-10 text-center text-sm text-slate-500">
                    No subtasks matched the current filters.
                </div>
            @endforelse
        </div>

        {{ $subtasks->links() }}
    </div>
@endsection
