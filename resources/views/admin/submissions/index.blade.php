<x-layout title="Submissions">
    <h1 class="text-2xl font-bold">Submissions</h1>
    <form class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-5">
        <select name="risk_level" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950"><option value="">All risk</option>@foreach(['normal','warning','suspicious'] as $risk)<option value="{{ $risk }}" @selected(request('risk_level')===$risk)>{{ ucfirst($risk) }}</option>@endforeach</select>
        <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filter</button>
    </form>
    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 dark:bg-slate-800"><tr><th class="p-3">Employee</th><th class="p-3">Site</th><th class="p-3">Reading</th><th class="p-3">Distance</th><th class="p-3">Risk</th><th class="p-3">Time</th></tr></thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach($submissions as $submission)
                <tr onclick="location.href='{{ route('submissions.show', $submission) }}'" class="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"><td class="p-3 font-medium">{{ $submission->employee?->name }}</td><td class="p-3">{{ $submission->site?->site_name }}</td><td class="p-3">Active Power: {{ $submission->active_power }} · Unit: {{ $submission->energy_reading }}</td><td class="p-3">{{ $submission->distance_from_site }}m</td><td class="p-3">{{ $submission->risk_level }}</td><td class="p-3">{{ $submission->created_at->format('d M Y H:i') }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $submissions->links() }}</div>
</x-layout>
