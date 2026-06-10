<?php
/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;

use frontend\assets\AppAsset;
AppAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html class="light" lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body
    class="bg-surface text-on-surface font-body-md selection:bg-secondary-container selection:text-on-secondary-container">
    <?php $this->beginBody() ?>

    <!-- Top Navigation -->
    <nav
        class="fixed top-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-margin-desktop h-16 bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline no-print">
        <div class="flex items-center gap-xs">
            <span class="material-symbols-outlined text-primary dark:text-inverse-primary">biotech</span>
            <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-on-primary-fixed">BioSketch
                Professional</h1>
        </div>
        <div class="flex items-center gap-md">
            <div class="hidden md:flex gap-sm">
                <?= Html::a('Public Profile', ['profile/view'], [
                    'class' => 'text-primary dark:text-secondary-fixed font-bold border-b-2 border-primary cursor-pointer transition-opacity active:opacity-80',
                ]) ?>
                <?= Html::a('Data Entry', ['researcher/create'], [
                    'class' => 'text-on-surface-variant dark:text-surface-variant hover:text-secondary dark:hover:text-secondary-fixed transition-colors',
                ]) ?>
            </div>
            <?= Html::img(
                'https://randomuser.me/api/portraits/men/75.jpg',
                ['alt' => 'Profile', 'class' => 'w-10 h-10 rounded-full border border-outline-variant object-cover']
            ) ?>
        </div>
    </nav>

    <!-- Main Content -->
    <main
        class="max-w-[1280px] mx-auto mt-16 px-margin-mobile md:px-margin-desktop py-lg flex flex-col md:flex-row gap-lg">
        <?= $this->render('_alerts') ?>
        <?= $content ?>
    </main>

    <!--  Footer -->
    <foo ter
        class="w-full py-sm px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center mt-lg bg-surface-container-lowest dark:bg-surface-container-low border-t border-outline-variant dark:border-outline no-print">
        <p class="font-label-caps text-label-caps text-on-surface-variant dark:text-surface-variant mb-md md:mb-0">
            &copy; <?= date('Y') ?> BioSketch Institutional. All rights reserved. Clinical Grade Precision.
        </p>
        <div class="flex gap-md">
            <?= Html::a('Compliance', ['site/compliance'], ['class' => 'text-on-surface-variant dark:text-surface-variant font-label-caps text-label-caps hover:text-primary dark:hover:text-inverse-primary transition-colors']) ?>
            <?= Html::a('Research Guidelines', ['site/guidelines'], ['class' => 'text-on-surface-variant dark:text-surface-variant font-label-caps text-label-caps hover:text-primary dark:hover:text-inverse-primary transition-colors']) ?>
            <?= Html::a('Help Desk', ['site/help'], ['class' => 'text-on-surface-variant dark:text-surface-variant font-label-caps text-label-caps hover:text-primary dark:hover:text-inverse-primary transition-colors']) ?>
        </div>
    </foo>

    <!-- Print FAB -->
    <button
        class="fixed bottom-margin-mobile right-margin-mobile w-12 h-12 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:opacity-90 active:scale-95 transition-all no-print"
        onclick="window.print()" aria-label="Print">
        <span class="material-symbols-outlined">print</span>
    </button>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>