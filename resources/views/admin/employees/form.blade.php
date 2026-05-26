<x-layout title="Employee Form">
    <h1 class="text-2xl font-bold">{{ $employee->exists ? 'Edit employee' : 'Add employee' }}</h1>
    <form method="post" action="{{ $employee->exists ? route('employees.update', $employee) : route('employees.store') }}" class="mt-6 grid gap-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:grid-cols-2">
        @csrf
        @if($employee->exists) @method('put') @endif
        @foreach(['employee_code'=>'Employee code','name'=>'Name','phone'=>'Phone','email'=>'Email'] as $field => $label)
            <label class="block text-sm font-medium">{{ $label }}<input name="{{ $field }}" value="{{ old($field, $employee->{$field}) }}" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">@error($field)<span class="text-red-600">{{ $message }}</span>@enderror</label>
        @endforeach
        <label class="block text-sm font-medium">Password<input type="password" name="password" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">@error('password')<span class="text-red-600">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-medium">Status<select name="status" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950"><option value="active" @selected(old('status', $employee->status)==='active')>Active</option><option value="inactive" @selected(old('status', $employee->status)==='inactive')>Inactive</option></select></label>
        <fieldset class="lg:col-span-2"><legend class="text-sm font-medium">Assigned sites</legend><div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">@foreach($sites as $site)<label class="flex items-center gap-2 rounded-md border border-slate-200 p-2 text-sm dark:border-slate-800"><input type="checkbox" name="site_ids[]" value="{{ $site->id }}" @checked(in_array($site->id, old('site_ids', $assigned), true))> {{ $site->site_name }}</label>@endforeach</div></fieldset>
        <div class="lg:col-span-2"><button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Save employee</button></div>
    </form>
</x-layout>
