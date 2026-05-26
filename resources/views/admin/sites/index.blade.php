<x-layout title="Sites">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Sites</h1>
        <a href="{{ route('sites.create') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Add site</a>
    </div>
    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 dark:bg-slate-800"><tr><th class="p-3">Code</th><th class="p-3">Name</th><th class="p-3">Radius</th><th class="p-3">Employees</th><th class="p-3">Submissions</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach($sites as $site)
                <tr><td class="p-3 font-medium">{{ $site->site_code }}</td><td class="p-3">{{ $site->site_name }}</td><td class="p-3">{{ $site->allowed_radius }}m</td><td class="p-3">{{ $site->employees_count }}</td><td class="p-3">{{ $site->submissions_count }}</td><td class="p-3">{{ $site->status }}</td><td class="p-3 text-right"><a class="font-medium text-blue-600" href="{{ route('sites.show', $site) }}">Open</a></td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sites->links() }}</div>
</x-layout>
