<?php

/** @var yii\web\View $this */
/** @var frontend\models\RoleForm $model */
/** @var yii\rbac\Permission[] $permissions */
/** @var bool $isUpdate */

$this->title = 'Edit Role: ' . $model->originalName;
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('_page_header', [
        'title' => 'Edit Role',
        'subtitle' => $model->originalName,
    ]) ?>
    <?= $this->render('_role_form', ['model' => $model, 'permissions' => $permissions]) ?>
</div>
