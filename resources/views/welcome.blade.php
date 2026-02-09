<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'TransFlo Instruments') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f5f7] text-slate-900">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(15,23,42,0.06),_transparent_55%),radial-gradient(circle_at_bottom,_rgba(59,130,246,0.08),_transparent_60%)]"></div>
        <div class="relative mx-auto flex min-h-screen max-w-5xl flex-col px-6 py-10">
            <nav class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/transflo-logo.jpg') }}" alt="TransFlo Instruments" class="h-10 w-auto object-contain" />
                    <div class="leading-tight">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">TransFlo Instruments</p>
                        <p class="font-[\"IBM Plex Mono\"] text-xs uppercase tracking-[0.4em] text-slate-400">Secure Systems</p>
                    </div>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:translate-y-[-1px] hover:shadow-md">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-slate-900/20 bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:translate-y-[-1px] hover:shadow-md">Log in</a>
                        @endauth
                    </div>
                @endif
            </nav>

            <main class="mt-20 flex flex-1 flex-col items-center justify-center text-center">
                <div class="max-w-2xl rounded-3xl border border-slate-900/10 bg-white/85 px-8 py-12 shadow-xl">
                    <p class="font-[\"IBM Plex Mono\"] text-xs uppercase tracking-[0.5em] text-slate-500">TransFlo Instruments</p>
                    <h1 class="mt-4 text-4xl font-semibold tracking-tight md:text-5xl">Planning &amp; Analysis Tooling</h1>
                    <p class="mt-4 text-sm font-semibold uppercase tracking-[0.4em] text-slate-500">Secure Access Portal</p>

                    <div class="mt-8 flex justify-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:translate-y-[-1px] hover:shadow-md">Enter Portal</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:translate-y-[-1px] hover:shadow-md">Sign in</a>
                        @endauth
                        <a href="mailto:it-support@transflo.local" class="inline-flex items-center rounded-full border border-slate-900/20 bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:translate-y-[-1px] hover:shadow-md">Request access</a>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-6 text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">
                    <span>Controlled Information</span>
                    <span class="h-4 w-px bg-slate-300"></span>
                    <span>Authorized Personnel Only</span>
                    <span class="h-4 w-px bg-slate-300"></span>
                    <span>Audit Logged</span>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
