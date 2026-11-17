<?php

/** @var yii\web\View $this */
/** @var common\models\User[] $users */
/** @var array<int, string[]> $rolesByUser */
/** @var yii\rbac\Role[] $allRoles */

use common\library\FormUi;
use yii\helpers\Html;

$this->title = 'User Role Assignment';
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('../_page_header', [
        'title' => 'User Role Assignment',
        'subtitle' => 'New accounts get the "researcher" role automatically. Assign "drd" or "admin" here.',
    ]) ?>

    <div class="<?= FormUi::gridContainerClass() ?> rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="<?= FormUi::thClass() ?>">User</th>
                        <th class="<?= FormUi::thClass() ?>">Staff ID</th>
                        <th class="<?= FormUi::thClass() ?>">Roles</th>
                        <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="<?= FormUi::trClass() ?>">
                            <td class="<?= FormUi::tdClass('primary') ?>">
                                <?= Html::encode($user->username) ?>
                                <div class="text-xs font-normal text-on-surface-variant"><?= Html::encode($user->email) ?></div>
                            </td>
                            <td class="<?= FormUi::tdClass('mono') ?>"><?= Html::encode($user->staff_id ?? '—') ?></td>
                            <td class="<?= FormUi::tdClass() ?>">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($rolesByUser[$user->id] ?? [] as $role): ?>
                                        <?= FormUi::chip($role, $role === 'admin' ? 'error' : ($role === 'drd' ? 'tertiary' : 'secondary')) ?>
                                    <?php endforeach; ?>
                                    <?= empty($rolesByUser[$user->id]) ? FormUi::badge('no role', 'warning') : '' ?>
                                </div>
                            </td>
                            <td class="<?= FormUi::tdClass() ?> text-right">
                                <?= FormUi::actionBtn('manage_accounts', ['assign-role', 'userId' => $user->id], 'edit', ['title' => 'Assign roles']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="<?= FormUi::gridFooterClass() ?>">
            <span class="text-on-surface-variant text-sm"><?= count($users) ?> user(s)</span>
        </div>
    </div>
</div>
