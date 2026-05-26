<x-layout title="Dashboard">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Operations Dashboard</h1>
            <p class="text-sm text-slate-500">Live audit view for site readings, GPS variance, and suspicious indicators.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Total employees', $cards['total_employees']],
            ['Total sites', $cards['total_sites']],
            ["Today's submissions", $cards['todays_submissions']],
            ['Suspicious submissions', $cards['suspicious_submissions']],
            ['Offline employees', $cards['offline_employees']],
        ] as [$label, $value])
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-bold">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2">
            <h2 class="mb-3 text-lg font-semibold">Live Map</h2>
            <livewire:live-map />
        </section>
        <section>
            <h2 class="mb-3 text-lg font-semibold">Suspicious Activity</h2>
            <livewire:suspicious-activity-feed />
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold">Active Power Trend</h2>
            <div id="voltage-chart" class="mt-4"></div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold">Latest Submissions</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($latestSubmissions as $submission)
                        <tr>
                            <td class="py-3 font-medium">{{ $submission->employee?->name }}</td>
                            <td class="py-3">{{ $submission->site?->site_name }}</td>
                            <td class="py-3">{{ $submission->distance_from_site }}m</td>
                            <td class="py-3"><span class="rounded-full px-2 py-1 text-xs {{ $submission->risk_level === 'suspicious' ? 'bg-red-100 text-red-700' : ($submission->risk_level === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">{{ $submission->risk_level }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('load', () => {
                new ApexCharts(document.querySelector('#voltage-chart'), {
                    chart: { type: 'line', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Avg active power', data: @json($voltageTrend->pluck('value')) }],
                    xaxis: { categories: @json($voltageTrend->pluck('day')) },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#2563eb']
                }).render();
            });
        </script>
    @endpush
</x-layout>
