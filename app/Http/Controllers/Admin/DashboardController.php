<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Submission;
use App\Models\SuspiciousLog;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(private readonly EmployeeRepository $employees)
    {
    }

    public function __invoke()
    {
        $today = Carbon::today();

        return view('admin.dashboard.index', [
            'cards' => [
                'total_employees' => Employee::count(),
                'total_sites' => Site::count(),
                'todays_submissions' => Submission::whereDate('created_at', $today)->count(),
                'suspicious_submissions' => Submission::where('suspicious_flag', true)->count(),
                'offline_employees' => $this->employees->offlineCount(),
            ],
            'latestSubmissions' => Submission::with(['employee', 'site'])->latest()->limit(8)->get(),
            'suspiciousFeed' => SuspiciousLog::with(['employee', 'site', 'submission'])->latest()->limit(8)->get(),
            'mapSubmissions' => Submission::with(['employee', 'site'])->latest()->limit(150)->get(),
            'sites' => Site::query()->where('status', 'active')->get(),
            'voltageTrend' => Submission::query()
                ->selectRaw('DATE(created_at) as day, ROUND(AVG(active_power), 2) as value')
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
            'riskSummary' => Submission::query()
                ->selectRaw('risk_level, COUNT(*) as total')
                ->groupBy('risk_level')
                ->pluck('total', 'risk_level'),
        ]);
    }
}
