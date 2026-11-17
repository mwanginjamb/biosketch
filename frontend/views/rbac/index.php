<?php

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Roles';
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('_page_header', [
        'title' => 'Roles',
        'subtitle' => 'Roles group permissions. Users get access through the roles assigned to them.',
        'actions' => FormUi::ctaButton('New Role', 'add', ['create-role']),
    ]) ?>

    <div class="<?= FormUi::gridContainerClass() ?> rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="<?= FormUi::thClass() ?>">Role</th>
                        <th class="<?= FormUi::thClass() ?>">Description</th>
                        <th class="<?= FormUi::thClass() ?>">Permissions (incl. inherited)</th>
                        <th class="<?= FormUi::thClass() ?>">Users</th>
                        <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->getModels() as $row): ?>
                        <tr class="<?= FormUi::trClass() ?>">
                            <td class="<?= FormUi::tdClass('primary') ?>">
                                <?= Html::encode($row['name']) ?>
                                <?php if ($row['protected']): ?>
                                    <?= FormUi::badge('built-in', 'tertiary') ?>
                                <?php endif; ?>
                                <?php foreach ($row['childRoles'] as $childRole): ?>
                                    <div class="text-xs font-normal text-on-surface-variant">includes <?= Html::encode($childRole) ?></div>
                                <?php endforeach; ?>
                            </td>
                            <td class="<?= FormUi::tdClass('muted') ?> whitespace-normal"><?= Html::encode($row['description'] ?: '—') ?></td>
                            <td class="<?= FormUi::tdClass() ?>">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($row['permissions'] as $permission): ?>
                                        <?= FormUi::chip($permission) ?>
                                    <?php endforeach; ?>
                                    <?= $row['permissions'] ? '' : '—' ?>
                                </div>
                            </td>
                            <td class="<?= FormUi::tdClass('mono') ?>"><?= (int) $row['users'] ?></td>
                            <td class="<?= FormUi::tdClass() ?> text-right whitespace-nowrap">
                                <?= FormUi::actionBtn('edit_square', ['update-role', 'name' => $row['name']], 'edit', ['title' => 'Edit role']) ?>
                                <?= FormUi::actionBtn('account_tree', ['manage-hierarchy', 'name' => $row['name']], 'view', ['title' => 'Manage hierarchy']) ?>
                                <?php if (!$row['protected']): ?>
                                    <?= FormUi::actionBtn('delete', ['delete-role', 'name' => $row['name']], 'delete', [
                                        'title' => 'Delete role',
                                        'data' => ['confirm' => "Delete role \"{$row['name']}\"? Users lose it immediately.", 'method' => 'post'],
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="<?= FormUi::gridFooterClass() ?>">
            <span class="text-on-surface-variant text-sm"><?= $dataProvider->getTotalCount() ?> role(s)</span>
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'flex gap-xs']]) ?>
        </div>
    </div>
</div>
