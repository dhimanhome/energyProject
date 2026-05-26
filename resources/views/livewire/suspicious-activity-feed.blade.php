<div wire:poll.30s class="space-y-3">
    @forelse($logs as $log)
        <a href="{{ $log->submission ? route('submissions.show', $log->submission) : '#' }}" class="block rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-red-300 dark:border-slate-800 dark:bg-slate-950">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm font-semibold text-slate-950 dark:text-white">{{ str_replace('_', ' ', ucfirst($log->type)) }}</span>
                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $log->severity === 'critical' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $log->severity }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $log->message }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $log->employee?->name }} · {{ $log->site?->site_name }} · {{ $log->created_at->diffForHumans() }}</p>
        </a>
    @empty
        <div class="rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-950">No suspicious activity yet.</div>
    @endforelse
</div>
