<div wire:poll.30s class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
    <div id="live-map" class="h-[420px] w-full"></div>
    <script>
        document.addEventListener('livewire:navigated', initLiveMap);
        document.addEventListener('DOMContentLoaded', initLiveMap);

        function initLiveMap() {
            const element = document.getElementById('live-map');
            if (!element || element.dataset.ready) return;
            element.dataset.ready = '1';

            const sites = @json($sites);
            const submissions = @json($submissions);
            const center = sites[0] ? [Number(sites[0].latitude), Number(sites[0].longitude)] : [28.4595, 77.0266];
            const map = L.map(element).setView(center, 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            sites.forEach(site => {
                L.circle([Number(site.latitude), Number(site.longitude)], {
                    radius: Number(site.allowed_radius),
                    color: '#2563eb',
                    fillColor: '#60a5fa',
                    fillOpacity: 0.12
                }).addTo(map).bindPopup(`<strong>${site.site_name}</strong><br>${site.site_code}<br>Allowed radius: ${site.allowed_radius}m`);
            });

            submissions.forEach(item => {
                const color = item.risk_level === 'suspicious' ? '#dc2626' : (item.risk_level === 'warning' ? '#ca8a04' : '#16a34a');
                const marker = L.circleMarker([Number(item.latitude), Number(item.longitude)], {
                    radius: 8,
                    color,
                    fillColor: color,
                    fillOpacity: 0.9
                }).addTo(map);
                marker.bindPopup(`
                    <strong>${item.employee?.name ?? 'Employee'}</strong><br>
                    Site: ${item.site?.site_name ?? '-'}<br>
                    Distance: ${item.distance_from_site}m<br>
                    Active power: ${item.active_power}<br>
                    Unit: ${item.energy_reading}<br>
                    Time: ${new Date(item.created_at).toLocaleString()}
                `);
            });
        }
    </script>
</div>
