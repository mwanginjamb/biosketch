<?php
use yii\bootstrap5\Html;
use yii\helpers\Url;
/** @var yii\web\View $this */
$this->render('_head');
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="light">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body
    class="bg-background text-on-background font-body-md clinical-gradient min-h-screen flex flex-col items-center justify-center">
    <?php $this->beginBody(); ?>

    <!-- Institutional band -->
    <div class="w-full bg-primary text-on-primary border-b-4 border-accent shadow-md">
        <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop h-14 flex items-center justify-between">
            <span class="flex items-center gap-xs font-bold">
                <span class="material-symbols-outlined text-accent-light" aria-hidden="true">biotech</span>
                BioSketch Professional
            </span>
            <span class="hidden sm:inline font-label-caps text-label-caps tracking-widest text-accent-light uppercase">
                Kenya Medical Research Institute
            </span>
        </div>
    </div>

    <main class="w-full max-w-[1280px] px-margin-mobile md:px-margin-desktop py-xl flex flex-col items-center">
        <div
            class="w-full max-w-[440px] bg-surface-container-lowest border border-outline-variant border-t-4 border-t-primary p-lg md:p-xl shadow-md transition-all duration-300">
            <?= $this->render('_alerts') ?>
            <?= $content ?>
        </div>

        <div class="mt-xl text-center max-w-[440px]">
            <div class="flex items-center justify-center gap-xs text-on-surface-variant mb-sm">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="font-label-caps text-label-caps tracking-wide">All Institutional Systems Operational</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant opacity-70">
                Unauthorized access is strictly prohibited. Your session is monitored in compliance with ICT Policies.
            </p>
        </div>
    </main>

    <div
        class="fixed top-1/4 -left-20 w-80 h-80 bg-accent opacity-20 blur-[100px] pointer-events-none rounded-full -z-10">
    </div>
    <div
        class="fixed bottom-1/4 -right-20 w-64 h-64 bg-primary-container opacity-20 blur-[100px] pointer-events-none rounded-full -z-10">
    </div>

    <footer
        class="w-full py-sm px-margin-desktop flex flex-col md:flex-row justify-between items-center mt-lg border-t border-outline-variant bg-surface-container-lowest">
        <div class="font-label-caps text-label-caps tracking-widest text-on-surface-variant mb-sm md:mb-0">
            © <?= date('Y') ?> KEMRI Institutional Researcher Scientist BioSketch. All rights reserved.
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