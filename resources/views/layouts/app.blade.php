<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'TaskFlow') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=space-grotesk:500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.95),_rgba(246,250,248,0.92)_38%,_rgba(230,242,248,0.85)_100%)] text-[color:var(--color-ink)]">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-24 right-[-4rem] h-72 w-72 rounded-full bg-[color:var(--color-mint)]/25 blur-3xl"></div>
            <div class="absolute bottom-0 left-[-6rem] h-80 w-80 rounded-full bg-[color:var(--color-coral)]/16 blur-3xl"></div>
        </div>

        <div class="relative">
            @auth
                <header class="border-b border-white/60 bg-white/70 backdrop-blur-xl">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">TaskFlow</p>
                            <a href="{{ route('tasks.index') }}" class="font-[family:var(--font-display)] text-2xl font-semibold text-slate-900">
                                Task Management Hub
                            </a>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden rounded-full border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-600 sm:block">
                                {{ auth()->user()->name }}
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>
            @endauth

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                        <p class="font-semibold">Please review the highlighted form details.</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
