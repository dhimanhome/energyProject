<?php

namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubmissionRepository
{
    public function latest(int $limit = 10)
    {
        return Submission::query()
            ->with(['employee', 'site'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function filtered(array $filters = []): LengthAwarePaginator
    {
        return Submission::query()
            ->with(['employee', 'site'])
            ->when($filters['risk_level'] ?? null, fn ($query, $risk) => $query->where('risk_level', $risk))
            ->when($filters['employee_id'] ?? null, fn ($query, $id) => $query->where('employee_id', $id))
            ->when($filters['site_id'] ?? null, fn ($query, $id) => $query->where('site_id', $id))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(25)
            ->withQueryString();
    }
}
