<x-layout title="Site Form">
    <h1 class="text-2xl font-bold">{{ $site->exists ? 'Edit site' : 'Add site' }}</h1>
    <form method="post" action="{{ $site->exists ? route('sites.update', $site) : route('sites.store') }}" class="mt-6 grid gap-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:grid-cols-2">
        @csrf
        @if($site->exists) @method('put') @endif
        @foreach([
            'site_code' => 'Site code',
            'site_name' => 'Site name',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'allowed_radius' => 'Allowed radius (m)',
        ] as $field => $label)
            <label class="block text-sm font-medium">{{ $label }}<input name="{{ $field }}" value="{{ old($field, $site->{$field}) }}" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">@error($field)<span class="text-red-600">{{ $message }}</span>@enderror</label>
        @endforeach
        <label class="block text-sm font-medium">Status<select name="status" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950"><option value="active" @selected(old('status', $site->status)==='active')>Active</option><option value="inactive" @selected(old('status', $site->status)==='inactive')>Inactive</option></select></label>
        <label class="block text-sm font-medium lg:col-span-2">Address<textarea name="address" rows="3" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">{{ old('address', $site->address) }}</textarea></label>
        <fieldset class="lg:col-span-2">
            <legend class="text-sm font-medium">Assigned employees</legend>
            <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($employees as $employee)
                    <label class="flex items-center gap-2 rounded-md border border-slate-200 p-2 text-sm dark:border-slate-800"><input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" @checked(in_array($employee->id, old('employee_ids', $assigned), true))> {{ $employee->name }}</label>
                @endforeach
            </div>
        </fieldset>
        <div class="lg:col-span-2"><button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Save site</button></div>
    </form>
</x-layout>
