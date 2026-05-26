<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('admin.employees.index', [
            'employees' => Employee::withCount(['sites', 'submissions', 'suspiciousLogs'])->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.employees.form', [
            'employee' => new Employee(['status' => 'active']),
            'sites' => Site::orderBy('site_name')->get(),
            'assigned' => [],
        ]);
    }

    public function store(EmployeeRequest $request)
    {
        $employee = DB::transaction(function () use ($request): Employee {
            $data = $request->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'status' => $data['status'],
            ]);
            $user->assignRole('Employee');

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'status' => $data['status'],
            ]);
            $employee->sites()->sync($data['site_ids'] ?? []);

            return $employee;
        });

        return redirect()->route('employees.show', $employee)->with('status', 'Employee created.');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', [
            'employee' => $employee->load(['sites', 'submissions.site', 'locationUpdates', 'suspiciousLogs.site']),
            'timeline' => $employee->submissions()->with('site')->latest()->limit(50)->get(),
            'locations' => $employee->locationUpdates()->latest()->limit(100)->get()->reverse()->values(),
        ]);
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.form', [
            'employee' => $employee,
            'sites' => Site::orderBy('site_name')->get(),
            'assigned' => $employee->sites()->pluck('sites.id')->all(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee): void {
            $data = $request->validated();
            $employee->update([
                'employee_code' => $data['employee_code'],
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'status' => $data['status'],
            ]);
            $employee->user->update(array_filter([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'] ?? null,
                'status' => $data['status'],
            ], fn ($value) => $value !== null));
            $employee->sites()->sync($data['site_ids'] ?? []);
        });

        return redirect()->route('employees.show', $employee)->with('status', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->user()->delete();

        return redirect()->route('employees.index')->with('status', 'Employee deleted.');
    }
}
