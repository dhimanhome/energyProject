<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        return Submission::query()
            ->with(['employee', 'site'])
            ->when($this->filters['risk_level'] ?? null, fn ($query, $risk) => $query->where('risk_level', $risk))
            ->when($this->filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($this->filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Employee', 'Site', 'Distance (m)', 'Risk', 'Active Power', 'Unit', 'Submitted At'];
    }

    public function map($submission): array
    {
        return [
            $submission->id,
            $submission->employee?->name,
            $submission->site?->site_name,
            $submission->distance_from_site,
            $submission->risk_level,
            $submission->active_power,
            $submission->energy_reading,
            $submission->created_at->toDateTimeString(),
        ];
    }
}
