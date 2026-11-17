<?php

/** @var yii\web\View $this */
/** @var yii\rbac\Item $item */
/** @var bool $isRole */
/** @var yii\rbac\Item[] $candidates */
/** @var yii\rbac\Item[] $currentChildren */

use common\library\FormUi;
use yii\helpers\Html;
use yii\rbac\Item;

$this->title = 'Hierarchy: ' . $item->name;

$roles = array_filter($candidates, fn(Item $c) => $c->type === Item::TYPE_ROLE);
$permissions = array_filter($candidates, fn(Item $c) => $c->type === Item::TYPE_PERMISSION);
ksort($roles);
ksort($permissions);

$renderGroup = function (string $heading, array $items) use ($currentChildren): string {
    if (!$items) {
        return '';
    }
    $html = Html::tag('h3', Html::encode($heading), ['class' => 'font-label-caps text-label-caps text-on-surface-variant uppercase mb-xs mt-md']);
    $html .= Html::beginTag('div', ['class' => 'grid grid-cols-1 sm:grid-cols-2 gap-xs']);
    foreach ($items as $name => $candidate) {
        $html .= Html::tag('label',
            Html::checkbox('children[]', isset($currentChildren[$name]), ['value' => $name, 'class' => 'mt-1 w-4 h-4 text-primary'])
            . Html::tag('span',
                Html::tag('span', Html::encode($name), ['class' => 'font-data-mono text-sm block'])
                . Html::tag('span', Html::encode($candidate->description ?? ''), ['class' => 'text-xs text-on-surface-variant'])
            ),
            ['class' => 'flex items-start gap-xs p-xs border border-outline-variant rounded cursor-pointer hover:bg-surface-container']
        );
    }
    return $html . Html::endTag('div');
};
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('_page_header', [
        'title' => 'Manage Hierarchy',
        'subtitle' => ($isRole ? 'Role' : 'Permission') . ' "' . $item->name . '": choose what it includes. Members get everything its children grant.',
    ]) ?>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md md:p-lg max-w-3xl">
        <?= Html::beginForm(['manage-hierarchy', 'name' => $item->name], 'post') ?>
        <?= Html::hiddenInput('children', '') ?>

        <?= $isRole ? $renderGroup('Child roles', $roles) : '' ?>
        <?= $renderGroup('Permissions', $permissions) ?>

        <div class="flex flex-col sm:flex-row gap-sm pt-md">
            <?= Html::submitButton('Save Hierarchy', ['class' => FormUi::buttonClass()]) ?>
            <?= FormUi::secondaryButton('Cancel', 'close', $isRole ? ['index'] : ['permissions']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
