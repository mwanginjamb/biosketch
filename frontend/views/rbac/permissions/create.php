<?php

/** @var yii\web\View $this */
/** @var frontend\models\PermissionForm $model */
/** @var array<string, string> $parentOptions */

$this->title = 'New Permission';
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('../_page_header', ['title' => 'New Permission']) ?>
    <?= $this->render('_form', ['model' => $model, 'parentOptions' => $parentOptions]) ?>
</div>
