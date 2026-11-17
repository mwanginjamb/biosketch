<?php

/** @var yii\web\View $this */
/** @var frontend\models\RoleForm $model */
/** @var yii\rbac\Permission[] $permissions */

$this->title = 'New Role';
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('_page_header', ['title' => 'New Role', 'subtitle' => 'Create a role and choose its permissions.']) ?>
    <?= $this->render('_role_form', ['model' => $model, 'permissions' => $permissions]) ?>
</div>
