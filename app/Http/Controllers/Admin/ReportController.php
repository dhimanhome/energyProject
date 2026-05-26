<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SubmissionsExport;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['risk_level', 'from', 'to']);

        return view('admin.reports.index', [
            'filters' => $filters,
            'daily' => Submission::query()
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total, SUM(suspicious_flag) as suspicious')
                ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
                ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
                ->groupBy('day')
                ->latest('day')
                ->limit(31)
                ->get(),
        ]);
    }

    public function csv(Request $request): StreamedResponse
    {
        $rows = (new SubmissionsExport($request->only(['risk_level', 'from', 'to'])))->collection();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Employee', 'Site', 'Distance (m)', 'Risk', 'Active Power', 'Unit', 'Submitted At']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->employee?->name,
                    $row->site?->site_name,
                    $row->distance_from_site,
                    $row->risk_level,
                    $row->active_power,
                    $row->energy_reading,
                    $row->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 'submissions-report.csv');
    }

    public function excel(Request $request)
    {
        return Excel::download(new SubmissionsExport($request->only(['risk_level', 'from', 'to'])), 'submissions-report.xlsx');
    }

    public function pdf(Request $request)
    {
        $submissions = (new SubmissionsExport($request->only(['risk_level', 'from', 'to'])))->collection();

        return Pdf::loadView('reports.submissions-pdf', compact('submissions'))->download('submissions-report.pdf');
    }
}
