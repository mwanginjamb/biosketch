<?php

/** @var yii\web\View $this */
/** @var string $title */
/** @var string|null $subtitle */
/** @var string|null $actions pre-rendered HTML (buttons) */

use yii\helpers\Html;

?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-md mb-lg">
    <div>
        <p class="flex items-center gap-xs font-data-mono text-xs text-secondary uppercase tracking-[0.2em] mb-xs"><span class="inline-block w-8 h-[3px] bg-brand-red" aria-hidden="true"></span>RBAC Management</p>
        <h1 class="font-display text-display text-primary tracking-tight"><?= Html::encode($title) ?></h1>
        <?php if (!empty($subtitle)): ?>
            <p class="text-on-surface-variant font-body-lg"><?= Html::encode($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if (!empty($actions)): ?>
        <div class="flex gap-xs"><?= $actions ?></div>
    <?php endif; ?>
</div>
