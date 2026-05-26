<x-layout title="Login">
    <div class="flex min-h-screen items-center justify-center">
        <form method="post" action="{{ route('login') }}" class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @csrf
            <h1 class="text-2xl font-bold">Power Audit Login</h1>
            <p class="mt-2 text-sm text-slate-500">Admin, supervisor, and employee access.</p>
            <label class="mt-6 block text-sm font-medium">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <label class="mt-4 block text-sm font-medium">Password</label>
            <input name="password" type="password" required class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
            <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded"> Remember me</label>
            <button class="mt-6 w-full rounded-md bg-slate-950 px-4 py-2 font-semibold text-white dark:bg-white dark:text-slate-950">Sign in</button>
        </form>
    </div>
</x-layout>
