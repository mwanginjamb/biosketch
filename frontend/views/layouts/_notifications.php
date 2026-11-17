<?php

/**
 * Notification bell for researchers: biosketch sections with missing data or not updated recently.
 * Renders nothing for guests and for users without the researcher role.
 *
 * @var yii\web\View $this
 */

use common\components\RbacHelper;
use frontend\components\BiosketchChecklist;
use yii\helpers\Html;

if (Yii::$app->user->isGuest || !Yii::$app->user->can(RbacHelper::PERM_CREATE)) {
    return;
}

$items = BiosketchChecklist::forUser(Yii::$app->user);
$count = count($items);
$levelStyle = [
    BiosketchChecklist::LEVEL_ACTION => 'text-error',
    BiosketchChecklist::LEVEL_SUGGESTION => 'text-secondary',
    BiosketchChecklist::LEVEL_STALE => 'text-tertiary',
];
$label = $count === 0 ? 'Notifications: your BioSketch is up to date' : "Notifications: {$count} item(s) need attention";
?>
<details class="relative" data-notification-menu>
    <summary class="list-none cursor-pointer relative p-1 rounded-full hover:bg-surface-container-low focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
        aria-label="<?= Html::encode($label) ?>">
        <span class="material-symbols-outlined text-on-surface-variant" aria-hidden="true">notifications</span>
        <?php if ($count > 0): ?>
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-error text-on-error text-[10px] font-bold flex items-center justify-center"
                data-notification-count><?= $count > 9 ? '9+' : $count ?></span>
        <?php endif; ?>
    </summary>

    <div class="absolute right-0 mt-xs w-80 max-h-[70vh] overflow-y-auto bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg z-[60]">
        <div class="px-sm py-xs border-b border-outline-variant font-bold text-on-surface">BioSketch checklist</div>

        <?php if ($count === 0): ?>
            <div class="px-sm py-md text-sm text-on-surface-variant flex items-center gap-xs">
                <span class="material-symbols-outlined text-secondary" aria-hidden="true">task_alt</span>
                Your BioSketch is complete and up to date.
            </div>
        <?php else: ?>
            <ul class="divide-y divide-outline-variant">
                <?php foreach ($items as $item): ?>
                    <li data-notification="<?= Html::encode($item['key']) ?>">
                        <?= Html::a(
                            Html::tag('span', Html::encode($item['icon']), [
                                'class' => 'material-symbols-outlined mt-0.5 ' . ($levelStyle[$item['level']] ?? ''),
                                'aria-hidden' => 'true',
                            ])
                            . Html::tag('span',
                                Html::tag('span', Html::encode($item['title']), ['class' => 'block font-bold text-sm text-on-surface'])
                                . Html::tag('span', Html::encode($item['detail']), ['class' => 'block text-xs text-on-surface-variant'])
                            ),
                            $item['url'],
                            ['class' => 'flex items-start gap-xs px-sm py-xs hover:bg-surface-container-high transition-colors']
                        ) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</details>
<?php
$this->registerJs(<<<'JS'
document.addEventListener('click', function (e) {
    document.querySelectorAll('details[data-notification-menu][open]').forEach(function (menu) {
        if (!menu.contains(e.target)) { menu.removeAttribute('open'); }
    });
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('details[data-notification-menu][open]').forEach(function (menu) { menu.removeAttribute('open'); });
    }
});
JS, \yii\web\View::POS_READY, 'notification-menu');
