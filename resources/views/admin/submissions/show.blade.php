<x-layout title="Submission #{{ $submission->id }}">
    <h1 class="text-2xl font-bold">Submission #{{ $submission->id }}</h1>
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
            <div id="submission-map" class="h-96 rounded-md"></div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-500">Employee</p><p class="font-semibold">{{ $submission->employee?->name }}</p>
            <p class="mt-4 text-sm text-slate-500">Site</p><p class="font-semibold">{{ $submission->site?->site_name }}</p>
            <p class="mt-4 text-sm text-slate-500">Distance</p><p class="text-2xl font-bold">{{ $submission->distance_from_site }}m</p>
            <p class="mt-4 text-sm text-slate-500">Risk</p><p class="font-semibold">{{ $submission->risk_level }}</p>
            <p class="mt-4 text-sm text-slate-500">Reading</p><p>Active Power: {{ $submission->active_power }} · Unit: {{ $submission->energy_reading }}</p>
        </div>
    </div>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach(['photo_path' => 'Meter photo', 'equipment_photo_path' => 'Equipment photo'] as $field => $label)
            <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="font-semibold">{{ $label }}</h2>
                @if($submission->{$field})<img src="{{ Storage::url($submission->{$field}) }}" class="mt-4 max-h-96 rounded-md object-contain">@else<p class="mt-4 text-sm text-red-600">Missing</p>@endif
            </div>
        @endforeach
    </div>
    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Suspicious indicators</h2>@forelse($submission->suspiciousLogs as $log)<p class="mt-3 rounded-md border border-slate-200 p-3 text-sm dark:border-slate-800">{{ $log->severity }} · {{ $log->message }}</p>@empty<p class="mt-3 text-sm text-slate-500">No suspicious indicators.</p>@endforelse</div>
    @push('scripts')<script>window.addEventListener('load',()=>{const site=[{{ $submission->site->latitude }},{{ $submission->site->longitude }}];const sub=[{{ $submission->latitude }},{{ $submission->longitude }}];const map=L.map('submission-map').setView(sub,14);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap'}).addTo(map);L.circle(site,{radius:{{ $submission->site->allowed_radius }},color:'#2563eb'}).addTo(map).bindPopup('Assigned site');L.marker(sub).addTo(map).bindPopup('Submission location');L.polyline([site,sub],{color:'#dc2626'}).addTo(map);});</script>@endpush
</x-layout>
