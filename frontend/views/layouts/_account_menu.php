<?php

/**
 * Avatar + account dropdown for the app headers.
 *
 * @var yii\web\View $this
 * @var string $profilePhoto image URL (photo or placeholder)
 * @var string $sizeClass Tailwind size classes for the avatar, e.g. 'w-12 h-12'
 */

use common\components\RbacHelper;
use yii\helpers\Html;

$sizeClass = $sizeClass ?? 'w-12 h-12';
$webUser = Yii::$app->user;
if ($webUser->isGuest) {
    return;
}
$researcher = $webUser->identity->researcher;

$itemClass = 'flex items-center gap-xs px-sm py-xs text-on-surface hover:bg-surface-container-high transition-colors w-full text-left';
$icon = static fn(string $name): string => Html::tag('span', $name, ['class' => 'material-symbols-outlined text-[20px]', 'aria-hidden' => 'true']);
?>
<details class="relative" data-account-menu>
    <summary class="list-none cursor-pointer rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-light" aria-label="Account menu">
        <span class="block <?= Html::encode($sizeClass) ?> rounded-full overflow-hidden border-2 border-accent-light">
            <img alt="" class="w-full h-full object-cover" src="<?= Html::encode($profilePhoto) ?>">
        </span>
    </summary>

    <div class="absolute right-0 top-full mt-xs w-64 max-w-[calc(100vw-1.5rem)] bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg py-xs z-[60]">
        <div class="px-sm py-xs border-b border-outline-variant mb-xs">
            <div class="font-bold text-on-surface truncate"><?= Html::encode($webUser->identity->username) ?></div>
            <div class="text-xs text-on-surface-variant truncate"><?= Html::encode($webUser->identity->email) ?></div>
        </div>

        <?php if ($researcher): ?>
            <?= Html::a($icon('person') . ' My BioSketch', ['/researcher/view', 'id' => $researcher->id], ['class' => $itemClass]) ?>
            <?= Html::a($icon('edit_square') . ' Update BioSketch', ['/researcher/update', 'id' => $researcher->id], ['class' => $itemClass]) ?>
            <?= Html::a($icon('picture_as_pdf') . ' BioSketch PDF', ['/researcher/report', 'id' => $researcher->id], ['class' => $itemClass]) ?>
        <?php elseif ($webUser->can(RbacHelper::PERM_CREATE)): ?>
            <?= Html::a($icon('add_circle') . ' Create BioSketch', ['/researcher/create'], ['class' => $itemClass]) ?>
        <?php endif; ?>

        <?php if ($webUser->can(RbacHelper::PERM_VIEW_LIST)): ?>
            <?= Html::a($icon('list') . ' BioSketch List', ['/site/index'], ['class' => $itemClass]) ?>
        <?php endif; ?>

        <div class="border-t border-outline-variant mt-xs pt-xs">
            <?= Html::beginForm(['/site/logout'], 'post') ?>
            <?= Html::submitButton($icon('logout') . ' Sign out', ['class' => $itemClass]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
</details>
<?php
// Close the menu when clicking outside it or pressing Escape.
$this->registerJs(<<<'JS'
document.addEventListener('click', function (e) {
    document.querySelectorAll('details[data-account-menu][open]').forEach(function (menu) {
        if (!menu.contains(e.target)) { menu.removeAttribute('open'); }
    });
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('details[data-account-menu][open]').forEach(function (menu) { menu.removeAttribute('open'); });
    }
});
JS, \yii\web\View::POS_READY, 'account-menu');
