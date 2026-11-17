<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string|null $videoUrl */

$user = Yii::$app->user;
$researcher = $user->isGuest ? null : $user->identity->researcher;
?>
<div class="max-w-4xl w-full space-y-lg">
    <div class="text-center space-y-sm">
        <h1 class="font-display text-display text-primary">How to use BioSketch Pro</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mx-auto">
            A three-minute walkthrough: signing up, building each section of your biosketch, and exporting it as a PDF.
        </p>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md shadow-md">
        <?php if ($videoUrl !== null): ?>
            <video class="w-full aspect-video rounded-lg bg-primary" controls playsinline preload="metadata"
                controlslist="nodownload">
                <source src="<?= Html::encode($videoUrl) ?>" type="video/mp4">
                Your browser cannot play this video.
                <a class="text-secondary underline" href="<?= Html::encode($videoUrl) ?>">Download the tutorial</a> instead.
            </video>
        <?php else: ?>
            <div class="aspect-video rounded-lg bg-surface-container-low flex flex-col items-center justify-center gap-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl" aria-hidden="true">videocam_off</span>
                <p class="font-body-md text-body-md">The tutorial video is not available yet. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col sm:flex-row gap-sm justify-center">
        <a href="<?= Url::toRoute(['site/home']) ?>"
            class="inline-flex items-center justify-center gap-xs px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Back to home
        </a>
        <?php if ($user->isGuest): ?>
            <a href="<?= Url::toRoute(['site/signup']) ?>"
                class="inline-flex items-center justify-center gap-xs px-md py-sm rounded-lg bg-primary text-on-primary hover:bg-secondary transition-all">
                <span class="material-symbols-outlined" aria-hidden="true">person_add</span> Sign up
            </a>
        <?php elseif ($researcher === null): ?>
            <a href="<?= Url::toRoute(['researcher/create']) ?>"
                class="inline-flex items-center justify-center gap-xs px-md py-sm rounded-lg bg-primary text-on-primary hover:bg-secondary transition-all">
                <span class="material-symbols-outlined" aria-hidden="true">post_add</span> Create your BioSketch
            </a>
        <?php else: ?>
            <a href="<?= Url::toRoute($researcher->getProfileRoute()) ?>"
                class="inline-flex items-center justify-center gap-xs px-md py-sm rounded-lg bg-primary text-on-primary hover:bg-secondary transition-all">
                <span class="material-symbols-outlined" aria-hidden="true">person</span> View your BioSketch
            </a>
        <?php endif; ?>
    </div>
</div>
