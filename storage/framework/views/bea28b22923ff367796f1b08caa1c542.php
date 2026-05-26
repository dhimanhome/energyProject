<div wire:poll.30s class="space-y-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <a href="<?php echo e($log->submission ? route('submissions.show', $log->submission) : '#'); ?>" class="block rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-red-300 dark:border-slate-800 dark:bg-slate-950">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm font-semibold text-slate-950 dark:text-white"><?php echo e(str_replace('_', ' ', ucfirst($log->type))); ?></span>
                <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo e($log->severity === 'critical' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>"><?php echo e($log->severity); ?></span>
            </div>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300"><?php echo e($log->message); ?></p>
            <p class="mt-2 text-xs text-slate-500"><?php echo e($log->employee?->name); ?> · <?php echo e($log->site?->site_name); ?> · <?php echo e($log->created_at->diffForHumans()); ?></p>
        </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-950">No suspicious activity yet.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/dhiman/Documents/projects/energyProject/resources/views/livewire/suspicious-activity-feed.blade.php ENDPATH**/ ?>