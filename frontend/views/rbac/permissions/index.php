<?php

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Permissions';
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('../_page_header', [
        'title' => 'Permissions',
        'subtitle' => 'Individual capabilities. Permissions with a rule (e.g. isBiosketchOwner) only apply to the user\'s own records.',
        'actions' => FormUi::ctaButton('New Permission', 'add', ['create-permission']),
    ]) ?>

    <div class="<?= FormUi::gridContainerClass() ?> rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="<?= FormUi::thClass() ?>">Permission</th>
                        <th class="<?= FormUi::thClass() ?>">Description</th>
                        <th class="<?= FormUi::thClass() ?>">Rule</th>
                        <th class="<?= FormUi::thClass() ?>">Granted by</th>
                        <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->getModels() as $row): ?>
                        <tr class="<?= FormUi::trClass() ?>">
                            <td class="<?= FormUi::tdClass('mono') ?>">
                                <?= Html::encode($row['name']) ?>
                                <?php if ($row['protected']): ?>
                                    <?= FormUi::badge('built-in', 'tertiary') ?>
                                <?php endif; ?>
                            </td>
                            <td class="<?= FormUi::tdClass('muted') ?> whitespace-normal"><?= Html::encode($row['description'] ?: '—') ?></td>
                            <td class="<?= FormUi::tdClass('muted') ?>"><?= Html::encode($row['rule'] ?: '—') ?></td>
                            <td class="<?= FormUi::tdClass() ?>">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($row['parents'] as $parent): ?>
                                        <?= FormUi::chip($parent, 'secondary') ?>
                                    <?php endforeach; ?>
                                    <?= $row['parents'] ? '' : '—' ?>
                                </div>
                            </td>
                            <td class="<?= FormUi::tdClass() ?> text-right whitespace-nowrap">
                                <?= FormUi::actionBtn('edit_square', ['update-permission', 'name' => $row['name']], 'edit', ['title' => 'Edit permission']) ?>
                                <?= FormUi::actionBtn('account_tree', ['manage-hierarchy', 'name' => $row['name']], 'view', ['title' => 'Manage hierarchy']) ?>
                                <?php if (!$row['protected']): ?>
                                    <?= FormUi::actionBtn('delete', ['delete-permission', 'name' => $row['name']], 'delete', [
                                        'title' => 'Delete permission',
                                        'data' => ['confirm' => "Delete permission \"{$row['name']}\"?", 'method' => 'post'],
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="<?= FormUi::gridFooterClass() ?>">
            <span class="text-on-surface-variant text-sm"><?= $dataProvider->getTotalCount() ?> permission(s)</span>
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'flex gap-xs']]) ?>
        </div>
    </div>
</div>
