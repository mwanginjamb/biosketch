<?php

/** @var yii\web\View $this */
/** @var frontend\models\PermissionForm $model */
/** @var array<string, string> $parentOptions */
/** @var string[] $otherParents */

$this->title = 'Edit Permission: ' . $model->originalName;
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('../_page_header', ['title' => 'Edit Permission', 'subtitle' => $model->originalName]) ?>
    <?= $this->render('_form', [
        'model' => $model,
        'parentOptions' => $parentOptions,
        'otherParents' => $otherParents,
    ]) ?>
</div>
