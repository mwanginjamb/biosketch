<?php

/**
 * Top-left branding (logo + app name) shared by all layouts; links to the home page.
 *
 * @var yii\web\View $this
 * @var string|null $label     brand text (default "BioSketch Pro")
 * @var string|null $textClass classes for the brand text
 */

use yii\helpers\Html;
use yii\helpers\Url;

$label ??= 'BioSketch Pro';
$textClass ??= 'font-headline-md text-headline-md';

// Guests can't open site/index (it requires login), so send them to the public landing page.
$home = Yii::$app->user->isGuest ? ['/site/home'] : ['/site/index'];
?>
<a href="<?= Url::toRoute($home) ?>"
    class="flex items-center gap-sm text-on-primary hover:opacity-90 transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-light rounded">
    <img src="<?= Url::to('@web/icons/icon-144x144.png') ?>" alt="KEMRI" width="40" height="40"
        class="h-10 w-10 shrink-0 rounded-md bg-white p-0.5 object-contain">
    <span class="<?= Html::encode($textClass) ?> font-bold"><?= Html::encode($label) ?></span>
</a>
