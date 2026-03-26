<div class="space-y-2">
    <label for="title" class="text-sm font-semibold text-slate-700">Title</label>
    <input
        id="title"
        name="title"
        type="text"
        value="{{ old('title', $task?->title) }}"
        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400"
        required
    >
</div>

<div class="space-y-2">
    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400"
        placeholder="Add context for the task"
    >{{ old('description', $task?->description) }}</textarea>
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div class="space-y-2">
        <label for="user_id" class="text-sm font-semibold text-slate-700">Assign To</label>
        <select id="user_id" name="user_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400" required>
            <option value="">Select a user</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $task?->user_id) == $user->id)>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label for="status" class="text-sm font-semibold text-slate-700">Status</label>
        <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-slate-400">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? 'pending') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex flex-wrap gap-3 pt-2">
    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
        {{ $submitLabel }}
    </button>

    <a href="{{ route('tasks.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
        Cancel
    </a>
</div>
