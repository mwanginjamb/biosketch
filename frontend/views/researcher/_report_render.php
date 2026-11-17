<?php

/**
 * BioSketch PDF viewer page.
 *
 * @var yii\web\View $this
 * @var frontend\models\Researcher $model
 * @var bool $isOwner
 */

use common\components\RbacHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$displayName = trim(($model->title ? $model->title . ' ' : '') . $model->full_name);
$this->title = $displayName . ' - BioSketch PDF';

$pdfUrl = Url::to(['report-pdf', 'id' => $model->id]);
$downloadUrl = Url::to(['report-pdf', 'id' => $model->id, 'download' => 1]);

$btnPrimary = 'inline-flex items-center gap-xs px-md py-xs bg-primary text-on-primary rounded font-bold hover:bg-primary-container transition-colors';
$btnSecondary = 'inline-flex items-center gap-xs px-md py-xs bg-surface-container-lowest text-primary border border-outline-variant rounded font-bold hover:bg-secondary-container hover:text-on-secondary-container transition-colors';
$icon = static fn(string $name): string => Html::tag('span', $name, ['class' => 'material-symbols-outlined text-[20px]', 'aria-hidden' => 'true']);
?>

<div class="flex flex-col gap-md">

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-md">
        <div>
            <p class="flex items-center gap-xs font-data-mono text-xs text-secondary uppercase tracking-[0.2em] mb-xs">
                <span class="inline-block w-8 h-[3px] bg-brand-red" aria-hidden="true"></span>BioSketch PDF
            </p>
            <h1 class="font-headline-lg text-headline-lg text-primary"><?= Html::encode($displayName) ?></h1>
        </div>

        <nav class="flex flex-wrap gap-xs" aria-label="BioSketch actions">
            <?= Html::a(
                $icon('arrow_back') . ($isOwner ? ' Back to my BioSketch' : ' Back to profile'),
                ['view', 'id' => $model->id],
                ['class' => $btnSecondary]
            ) ?>
            <?php if (Yii::$app->user->can(RbacHelper::PERM_VIEW_LIST)): ?>
                <?= Html::a($icon('list') . ' BioSketch List', ['/site/index'], ['class' => $btnSecondary]) ?>
            <?php endif; ?>
            <?= Html::a($icon('home') . ' Home', ['/site/home'], ['class' => $btnSecondary]) ?>
            <?= Html::a($icon('open_in_new') . ' Open in new tab', $pdfUrl, [
                'class' => $btnSecondary,
                'target' => '_blank',
                'rel' => 'noopener',
            ]) ?>
            <?= Html::a($icon('download') . ' Download PDF', $downloadUrl, ['class' => $btnPrimary]) ?>
        </nav>
    </div>

    <!-- PDF -->
    <div class="bg-surface-container-lowest border border-outline-variant border-t-4 border-t-primary rounded shadow-sm overflow-hidden">
        <iframe src="<?= Html::encode($pdfUrl) ?>" title="BioSketch PDF for <?= Html::encode($displayName) ?>"
            class="w-full h-[80vh] border-0 block"></iframe>
    </div>

    <p class="text-sm text-on-surface-variant">
        PDF not showing? <?= Html::a('Download it instead', $downloadUrl, ['class' => 'text-secondary font-bold hover:underline']) ?>.
    </p>
</div>
