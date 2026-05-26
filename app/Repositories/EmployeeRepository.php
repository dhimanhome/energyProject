<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function active()
    {
        return Employee::query()->where('status', 'active')->orderBy('name')->get();
    }

    public function offlineCount(): int
    {
        return Employee::query()
            ->where(fn ($query) => $query->whereNull('last_seen')->orWhere('last_seen', '<', now()->subMinutes(10)))
            ->count();
    }
}
