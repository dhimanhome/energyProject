<x-layout title="{{ $site->site_name }}">
    <div class="flex items-center justify-between gap-4"><div><h1 class="text-2xl font-bold">{{ $site->site_name }}</h1><p class="text-sm text-slate-500">{{ $site->site_code }} · {{ $site->address }}</p></div><a class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700" href="{{ route('sites.edit', $site) }}">Edit</a></div>
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Coordinates</p><p class="mt-2 font-mono">{{ $site->latitude }}, {{ $site->longitude }}</p></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Allowed radius</p><p class="mt-2 text-2xl font-bold">{{ $site->allowed_radius }}m</p></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Assigned employees</p><p class="mt-2 text-2xl font-bold">{{ $site->employees->count() }}</p></div>
    </div>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Active power chart</h2><div id="site-voltage-chart" class="mt-4"></div></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Latest readings</h2>@foreach($latestSubmissions as $submission)<a href="{{ route('submissions.show', $submission) }}" class="mt-3 block rounded-md border border-slate-200 p-3 text-sm dark:border-slate-800">{{ $submission->employee?->name }} · Active Power {{ $submission->active_power }} · Unit {{ $submission->energy_reading }} · {{ $submission->distance_from_site }}m · {{ $submission->created_at->diffForHumans() }}</a>@endforeach</div>
    </div>
    @push('scripts')<script>window.addEventListener('load',()=>{new ApexCharts(document.querySelector('#site-voltage-chart'),{chart:{type:'area',height:260,toolbar:{show:false}},series:[{name:'Active Power',data:@json($voltageTrend->pluck('value'))}],xaxis:{categories:@json($voltageTrend->pluck('day'))},colors:['#0f766e']}).render();});</script>@endpush
</x-layout>
