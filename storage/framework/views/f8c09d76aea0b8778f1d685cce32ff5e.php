<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => ''.e($site->site_name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($site->site_name).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="flex items-center justify-between gap-4"><div><h1 class="text-2xl font-bold"><?php echo e($site->site_name); ?></h1><p class="text-sm text-slate-500"><?php echo e($site->site_code); ?> · <?php echo e($site->address); ?></p></div><a class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700" href="<?php echo e(route('sites.edit', $site)); ?>">Edit</a></div>
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Coordinates</p><p class="mt-2 font-mono"><?php echo e($site->latitude); ?>, <?php echo e($site->longitude); ?></p></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Allowed radius</p><p class="mt-2 text-2xl font-bold"><?php echo e($site->allowed_radius); ?>m</p></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><p class="text-sm text-slate-500">Assigned employees</p><p class="mt-2 text-2xl font-bold"><?php echo e($site->employees->count()); ?></p></div>
    </div>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Voltage chart</h2><div id="site-voltage-chart" class="mt-4"></div></div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><h2 class="font-semibold">Latest readings</h2><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><a href="<?php echo e(route('submissions.show', $submission)); ?>" class="mt-3 block rounded-md border border-slate-200 p-3 text-sm dark:border-slate-800"><?php echo e($submission->employee?->name); ?> · <?php echo e($submission->voltage); ?>V · <?php echo e($submission->distance_from_site); ?>m · <?php echo e($submission->created_at->diffForHumans()); ?></a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div>
    </div>
    <?php $__env->startPush('scripts'); ?><script>window.addEventListener('load',()=>{new ApexCharts(document.querySelector('#site-voltage-chart'),{chart:{type:'area',height:260,toolbar:{show:false}},series:[{name:'Voltage',data:<?php echo json_encode($voltageTrend->pluck('value'), 15, 512) ?>}],xaxis:{categories:<?php echo json_encode($voltageTrend->pluck('day'), 15, 512) ?>},colors:['#0f766e']}).render();});</script><?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH /Users/dhiman/Documents/projects/energyProject/resources/views/admin/sites/show.blade.php ENDPATH**/ ?>