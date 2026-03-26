@extends('layouts.app')

@section('content')
    <div class="grid min-h-[calc(100vh-8rem)] gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <section class="space-y-8">
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Role Based Workspace</p>
                <h1 class="max-w-2xl font-[family:var(--font-display)] text-5xl font-semibold leading-tight text-slate-950 sm:text-6xl">
                    Plan, assign, and finish work from one focused task board.
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-600">
                    Admins can manage users, tasks, and subtasks. Users see only their assigned work, can update status, and create subtasks without leaving the server-rendered flow.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <article class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-500">Admins</p>
                    <p class="mt-3 text-3xl font-[family:var(--font-display)] font-semibold text-slate-900">2 views</p>
                    <p class="mt-2 text-sm text-slate-600">Users, tasks, and subtasks all stay visible from one place.</p>
                </article>

                <article class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-500">Users</p>
                    <p class="mt-3 text-3xl font-[family:var(--font-display)] font-semibold text-slate-900">1 workflow</p>
                    <p class="mt-2 text-sm text-slate-600">Assigned tasks, status updates, subtasks, search, and pagination.</p>
                </article>

                <article class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-500">API</p>
                    <p class="mt-3 text-3xl font-[family:var(--font-display)] font-semibold text-slate-900">REST</p>
                    <p class="mt-2 text-sm text-slate-600">Forms submit directly to `/api/v1/*` endpoints with no JavaScript fetch.</p>
                </article>
            </div>

            <div class="rounded-[2rem] border border-slate-200/70 bg-slate-950 p-6 text-slate-100 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.8)]">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400">Demo Credentials</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-sm font-semibold text-white">Admin</p>
                        <p class="mt-2 text-sm text-slate-300">Email: `super@admin.com`</p>
                        <p class="text-sm text-slate-300">Password: `Pass@123`</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-sm font-semibold text-white">User</p>
                        <p class="mt-2 text-sm text-slate-300">Email: `user@test.com`</p>
                        <p class="text-sm text-slate-300">Password: `Pass@123`</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_30px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur">
            <div class="space-y-2">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Sign In</p>
                <h2 class="font-[family:var(--font-display)] text-3xl font-semibold text-slate-950">Open your workspace</h2>
                <p class="text-sm text-slate-600">Use a seeded admin or user account to explore the role-based system.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none ring-0 transition focus:border-slate-400"
                        placeholder="you@example.com"
                        required
                    >
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none ring-0 transition focus:border-slate-400"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Login
                </button>
            </form>
        </section>
    </div>
@endsection
