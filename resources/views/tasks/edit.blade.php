@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-3xl rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.45)] sm:p-8">
        <div class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Admin</p>
            <h1 class="font-[family:var(--font-display)] text-4xl font-semibold text-slate-950">Edit task</h1>
            <p class="text-sm text-slate-600">Update assignment details, task copy, and status in one place.</p>
        </div>

        <form method="POST" action="{{ route('api.tasks.update', $task) }}" class="mt-8 space-y-5">
            @csrf
            @method('PATCH')
            @include('tasks.partials.form', ['submitLabel' => 'Save Changes'])
        </form>
    </section>
@endsection
