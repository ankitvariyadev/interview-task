<article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">{{ $subtask->title }}</h3>
            @if ($subtask->description)
                <p class="mt-2 text-sm leading-7 text-slate-600">{{ $subtask->description }}</p>
            @endif
        </div>

        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]
            {{ $subtask->status->value === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($subtask->status->value === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
            {{ $subtask->status->label() }}
        </span>
    </div>

    @if ($subtask->nestedSubtasks->isNotEmpty())
        <div class="mt-4 space-y-4 border-l border-slate-200 pl-4">
            @foreach ($subtask->nestedSubtasks as $nestedSubtask)
                @include('tasks.partials.subtask-tree', ['subtask' => $nestedSubtask, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</article>
