<?php

/** @var yii\web\View $this */
/** @var common\models\User[] $users */

use common\components\RbacHelper;
use common\library\FormUi;
use common\models\User;
use yii\helpers\Html;

$this->title = 'System Users';

$statusBadge = static fn(int $status): string => match ($status) {
    User::STATUS_ACTIVE => FormUi::badge('active', 'secondary'),
    User::STATUS_INACTIVE => FormUi::badge('unverified', 'warning'),
    default => FormUi::badge('deleted', 'error'),
};
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('@frontend/views/rbac/_page_header', [
        'title' => 'System Users',
        'subtitle' => 'All accounts, their staff directory details and roles.',
    ]) ?>

    <div class="<?= FormUi::gridContainerClass() ?> rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="<?= FormUi::thClass() ?>">User</th>
                        <th class="<?= FormUi::thClass() ?>">Staff ID</th>
                        <th class="<?= FormUi::thClass() ?>">Department</th>
                        <th class="<?= FormUi::thClass() ?>">Status</th>
                        <th class="<?= FormUi::thClass() ?>">Roles</th>
                        <th class="<?= FormUi::thClass() ?>">Joined</th>
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
                            <td class="<?= FormUi::tdClass('muted') ?>">
                                <?= Html::encode($user->department ?? '—') ?>
                                <?php if ($user->department_code): ?>
                                    <span class="font-data-mono text-xs">(<?= Html::encode($user->department_code) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="<?= FormUi::tdClass() ?>"><?= $statusBadge((int) $user->status) ?></td>
                            <td class="<?= FormUi::tdClass() ?>">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach (RbacHelper::roleNamesForUser((int) $user->id) as $role): ?>
                                        <?= FormUi::chip($role) ?>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="<?= FormUi::tdClass('muted') ?>"><?= Yii::$app->formatter->asDate($user->created_at) ?></td>
                            <td class="<?= FormUi::tdClass() ?> text-right">
                                <?= FormUi::actionBtn('manage_accounts', ['/rbac/assign-role', 'userId' => $user->id], 'edit', ['title' => 'Assign roles']) ?>
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
