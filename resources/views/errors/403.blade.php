<x-layout title="Access denied">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-lg rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold uppercase tracking-wide text-red-600">403</p>
            <h1 class="mt-2 text-2xl font-bold">User does not have the right roles.</h1>
            <p class="mt-3 text-sm text-slate-500">You may be signed in with an employee account or an old browser session.</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('logout.reset') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Clear session</a>
                <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Back to login</a>
            </div>
        </div>
    </div>
</x-layout>
