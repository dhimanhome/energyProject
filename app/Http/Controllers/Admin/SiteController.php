<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteRequest;
use App\Models\Employee;
use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        return view('admin.sites.index', [
            'sites' => Site::withCount(['employees', 'submissions'])->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.sites.form', [
            'site' => new Site(['status' => 'active', 'allowed_radius' => 100]),
            'employees' => Employee::orderBy('name')->get(),
            'assigned' => [],
        ]);
    }

    public function store(SiteRequest $request)
    {
        $site = Site::create($request->safe()->except('employee_ids'));
        $site->employees()->sync($request->validated('employee_ids', []));

        return redirect()->route('sites.show', $site)->with('status', 'Site created.');
    }

    public function show(Site $site)
    {
        return view('admin.sites.show', [
            'site' => $site->load(['employees', 'submissions.employee']),
            'latestSubmissions' => $site->submissions()->with('employee')->latest()->limit(20)->get(),
            'voltageTrend' => $site->submissions()
                ->selectRaw('DATE(created_at) as day, ROUND(AVG(active_power), 2) as value')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
        ]);
    }

    public function edit(Site $site)
    {
        return view('admin.sites.form', [
            'site' => $site,
            'employees' => Employee::orderBy('name')->get(),
            'assigned' => $site->employees()->pluck('employees.id')->all(),
        ]);
    }

    public function update(SiteRequest $request, Site $site)
    {
        $site->update($request->safe()->except('employee_ids'));
        $site->employees()->sync($request->validated('employee_ids', []));

        return redirect()->route('sites.show', $site)->with('status', 'Site updated.');
    }

    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')->with('status', 'Site deleted.');
    }
}
