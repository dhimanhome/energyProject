<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Power Audit System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-full lg:flex">
        @auth
            <aside class="border-r border-slate-200 bg-white px-4 py-5 dark:border-slate-800 dark:bg-slate-900 lg:fixed lg:inset-y-0 lg:w-72">
                <div class="text-lg font-bold tracking-wide">Power Audit</div>
                <p class="mt-1 text-xs uppercase text-slate-500">Field monitoring</p>
                <nav class="mt-8 space-y-1">
                    @php
                        $items = [
                            ['Dashboard', 'dashboard', 'M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z'],
                            ['Live Employees', 'live-employees.index', 'M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11zM12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
                            ['Sites', 'sites.index', 'M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11z'],
                            ['Employees', 'employees.index', 'M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5 1.34 3.5 3 3.5zM8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11z'],
                            ['Submissions', 'submissions.index', 'M5 4h14v16H5zM8 8h8M8 12h8M8 16h5'],
                            ['Reports', 'reports.index', 'M4 19h16M7 16V8m5 8V5m5 11v-6'],
                        ];
                    @endphp
                    @foreach($items as [$label, $route, $path])
                        <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route)) ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $path }}" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
                <form class="mt-8" method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Sign out</button>
                </form>
            </aside>
        @endauth

        <main class="flex-1 {{ auth()->check() ? 'lg:pl-72' : '' }}">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @if(session('status'))
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>
