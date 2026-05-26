<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Power Audit System'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-full lg:flex">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <aside class="border-r border-slate-200 bg-white px-4 py-5 dark:border-slate-800 dark:bg-slate-900 lg:fixed lg:inset-y-0 lg:w-72">
                <div class="text-lg font-bold tracking-wide">Power Audit</div>
                <p class="mt-1 text-xs uppercase text-slate-500">Field monitoring</p>
                <nav class="mt-8 space-y-1">
                    <?php
                        $items = [
                            ['Dashboard', 'dashboard', 'M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z'],
                            ['Live Employees', 'live-employees.index', 'M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11zM12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
                            ['Sites', 'sites.index', 'M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11z'],
                            ['Employees', 'employees.index', 'M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5 1.34 3.5 3 3.5zM8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11z'],
                            ['Submissions', 'submissions.index', 'M5 4h14v16H5zM8 8h8M8 12h8M8 16h5'],
                            ['Reports', 'reports.index', 'M4 19h16M7 16V8m5 8V5m5 11v-6'],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route($route)); ?>" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium <?php echo e(request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route)) ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'); ?>">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?php echo e($path); ?>" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php echo e($label); ?>

                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </nav>
                <form class="mt-8" method="post" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Sign out</button>
                </form>
            </aside>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <main class="flex-1 <?php echo e(auth()->check() ? 'lg:pl-72' : ''); ?>">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800"><?php echo e(session('status')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($slot); ?>

            </div>
        </main>
    </div>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/dhiman/Documents/projects/energyProject/resources/views/components/layout.blade.php ENDPATH**/ ?>