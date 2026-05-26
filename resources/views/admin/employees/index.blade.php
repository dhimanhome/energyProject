<x-layout title="Employees">
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold">Employees</h1><a href="{{ route('employees.create') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Add employee</a></div>
    <div class="mt-6 grid gap-4">
        @foreach($employees as $employee)
            <a href="{{ route('employees.show', $employee) }}" class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:grid-cols-6">
                <div class="md:col-span-2"><p class="font-semibold">{{ $employee->name }}</p><p class="text-sm text-slate-500">{{ $employee->employee_code }} · {{ $employee->email }}</p></div>
                <p class="text-sm">Sites<br><strong>{{ $employee->sites_count }}</strong></p>
                <p class="text-sm">Submissions<br><strong>{{ $employee->submissions_count }}</strong></p>
                <p class="text-sm">Suspicious<br><strong>{{ $employee->suspicious_logs_count }}</strong></p>
                <p class="text-sm">Status<br><span class="{{ $employee->isOnline() ? 'text-emerald-600' : 'text-slate-500' }}">{{ $employee->isOnline() ? 'Online' : 'Offline' }}</span></p>
            </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $employees->links() }}</div>
</x-layout>
