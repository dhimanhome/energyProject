<x-layout title="Reports">
    <h1 class="text-2xl font-bold">Reports</h1>
    <form class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-5">
        <select name="risk_level" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950"><option value="">All risk</option>@foreach(['normal','warning','suspicious'] as $risk)<option value="{{ $risk }}" @selected(($filters['risk_level'] ?? '')===$risk)>{{ ucfirst($risk) }}</option>@endforeach</select>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Apply</button>
    </form>
    <div class="mt-4 flex flex-wrap gap-3"><a class="rounded-md border border-slate-300 px-4 py-2 text-sm" href="{{ route('reports.csv', request()->query()) }}">CSV</a><a class="rounded-md border border-slate-300 px-4 py-2 text-sm" href="{{ route('reports.excel', request()->query()) }}">Excel</a><a class="rounded-md border border-slate-300 px-4 py-2 text-sm" href="{{ route('reports.pdf', request()->query()) }}">PDF</a></div>
    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Daily report</h2>@foreach($daily as $row)<div class="mt-3 flex justify-between rounded-md border border-slate-200 p-3 text-sm dark:border-slate-800"><span>{{ $row->day }}</span><span>{{ $row->total }} submissions · {{ $row->suspicious }} suspicious</span></div>@endforeach</div>
</x-layout>
