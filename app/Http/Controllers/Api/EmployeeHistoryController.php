<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeHistoryController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        abort_if(! $employee, 403, 'Authenticated user is not linked to an employee profile.');

        return response()->json([
            'submissions' => $employee->submissions()->with('site')->latest()->paginate(30),
            'locations' => $employee->locationUpdates()->latest()->limit(100)->get(),
            'suspicious_count' => $employee->suspiciousLogs()->count(),
        ]);
    }
}
