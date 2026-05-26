<x-layout title="Live Employees">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Live Employees</h1>
            <p class="text-sm text-slate-500">Latest GPS ping from every field employee. Refreshes automatically.</p>
        </div>
        <div class="text-sm text-slate-500" id="live-refresh-status">Loading...</div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-4">
        <section class="xl:col-span-3">
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div id="employees-live-map" class="h-[620px] w-full"></div>
            </div>
        </section>
        <section>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="font-semibold">Employees</h2>
                <div id="employee-live-list" class="mt-4 space-y-3"></div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('load', () => {
                const map = L.map('employees-live-map').setView([28.4595, 77.0266], 10);
                const markers = new Map();
                const list = document.getElementById('employee-live-list');
                const status = document.getElementById('live-refresh-status');

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                function colorFor(employee) {
                    if (!employee.location) return '#64748b';
                    if (employee.location.age_seconds <= 120) return '#16a34a';
                    if (employee.location.age_seconds <= 600) return '#ca8a04';
                    return '#dc2626';
                }

                function employeeHtml(employee) {
                    const color = colorFor(employee);
                    const sites = employee.sites.length ? employee.sites.join(', ') : 'No sites assigned';
                    return `
                        <button data-employee-id="${employee.id}" class="w-full rounded-md border border-slate-200 p-3 text-left text-sm transition hover:border-blue-300 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold">${employee.name}</span>
                                <span class="h-2.5 w-2.5 rounded-full" style="background:${color}"></span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">${employee.employee_code} · ${employee.last_seen_human}</div>
                            <div class="mt-1 text-xs text-slate-500">${sites}</div>
                        </button>
                    `;
                }

                async function refreshEmployees() {
                    const response = await fetch('{{ route('live-employees.data') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const payload = await response.json();
                    const bounds = [];

                    list.innerHTML = payload.employees.map(employeeHtml).join('');

                    payload.employees.forEach(employee => {
                        if (!employee.location) return;

                        const point = [employee.location.latitude, employee.location.longitude];
                        bounds.push(point);
                        const color = colorFor(employee);
                        const popup = `
                            <strong>${employee.name}</strong><br>
                            Code: ${employee.employee_code}<br>
                            Phone: ${employee.phone || '-'}<br>
                            Last seen: ${employee.last_seen_human}<br>
                            Accuracy: ${employee.location.accuracy || '-'}m<br>
                            Sites: ${employee.sites.join(', ') || '-'}
                        `;

                        if (markers.has(employee.id)) {
                            markers.get(employee.id).setLatLng(point).setStyle({ color, fillColor: color }).setPopupContent(popup);
                        } else {
                            const marker = L.circleMarker(point, {
                                radius: 10,
                                color,
                                fillColor: color,
                                fillOpacity: 0.9
                            }).addTo(map).bindPopup(popup);
                            markers.set(employee.id, marker);
                        }
                    });

                    document.querySelectorAll('[data-employee-id]').forEach(button => {
                        button.addEventListener('click', () => {
                            const marker = markers.get(Number(button.dataset.employeeId));
                            if (marker) {
                                map.setView(marker.getLatLng(), 16);
                                marker.openPopup();
                            }
                        });
                    });

                    if (bounds.length) {
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
                    }

                    status.textContent = `Updated ${new Date(payload.generated_at).toLocaleTimeString()}`;
                }

                refreshEmployees();
                setInterval(refreshEmployees, 10000);
            });
        </script>
    @endpush
</x-layout>
