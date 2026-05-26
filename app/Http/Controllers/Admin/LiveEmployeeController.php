<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;

class LiveEmployeeController extends Controller
{
    public function index()
    {
        return view('admin.live-employees.index');
    }

    public function data()
    {
        $employees = Employee::query()
            ->with(['latestLocation', 'sites'])
            ->orderBy('name')
            ->get()
            ->map(function (Employee $employee): array {
                $location = $employee->latestLocation;

                return [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->name,
                    'phone' => $employee->phone,
                    'status' => $employee->status,
                    'online' => $employee->isOnline(),
                    'last_seen' => $employee->last_seen?->toIso8601String(),
                    'last_seen_human' => $employee->last_seen?->diffForHumans() ?? 'Never',
                    'sites' => $employee->sites->pluck('site_name')->values(),
                    'location' => $location ? [
                        'latitude' => (float) $location->latitude,
                        'longitude' => (float) $location->longitude,
                        'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                        'recorded_at' => $location->recorded_at?->toIso8601String(),
                        'updated_at' => $location->created_at->toIso8601String(),
                        'age_seconds' => $location->created_at->diffInSeconds(now()),
                    ] : null,
                ];
            });

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'employees' => $employees,
        ]);
    }
}
