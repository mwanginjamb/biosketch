<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="light">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
      <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body
    class="bg-background text-on-background font-body-md clinical-gradient min-h-screen flex flex-col items-center justify-center">
    <?php $this->beginBody(); ?>

    <main class="w-full max-w-[1280px] px-margin-mobile md:px-margin-desktop py-xl flex flex-col items-center">
        <div
            class="w-full max-w-[440px] bg-surface-container-lowest border border-outline-variant p-lg md:p-xl shadow-sm transition-all duration-300">
            <?= $this->render('_alerts') ?>
            <?= $content ?>
        </div>

        <div class="mt-xl text-center max-w-[440px]">
            <div class="flex items-center justify-center gap-xs text-on-surface-variant mb-sm">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="font-label-caps text-label-caps tracking-wide">All Institutional Systems Operational</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant opacity-70">
                Unauthorized access is strictly prohibited. Your session is monitored in compliance with Department of
                Biology security protocols.
            </p>
        </div>
    </main>

    <div
        class="fixed top-1/4 -left-20 w-80 h-80 bg-secondary-container opacity-10 blur-[100px] pointer-events-none rounded-full">
    </div>
    <div
        class="fixed bottom-1/4 -right-20 w-64 h-64 bg-primary-container opacity-5 blur-[100px] pointer-events-none rounded-full">
    </div>

    <footer
        class="w-full py-sm px-margin-desktop flex flex-col md:flex-row justify-between items-center mt-lg border-t border-outline-variant bg-surface-container-lowest">
        <div class="font-label-caps text-label-caps tracking-widest text-on-surface-variant mb-sm md:mb-0">
            © 2024 BioSketch Institutional. All rights reserved. Clinical Grade Precision.
        </div>
        <div class="flex gap-md">
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                href="#">Compliance</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                href="#">Research Guidelines</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                href="#">Help Desk</a>
        </div>
    </footer>

    <?php $this->endBody(); ?>
</body>

</html>
<?php $this->endPage(); ?>