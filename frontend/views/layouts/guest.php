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

<?php
// Views may widen the card (e.g. the two-column signup form): $this->params['cardWidth'] = 'max-w-[640px]';
$cardWidth = $this->params['cardWidth'] ?? 'max-w-[440px]';
?>
<body
    class="bg-background text-on-background font-body-md clinical-gradient min-h-dvh flex flex-col items-center">
    <?php $this->beginBody(); ?>

    <!-- Institutional band -->
    <div class="w-full bg-primary text-on-primary border-b-4 border-accent shadow-md">
        <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop h-14 flex items-center justify-between">
            <?= $this->render('_brand', ['label' => 'BioSketch Professional', 'textClass' => 'text-base']) ?>
            <span class="hidden sm:inline font-label-caps text-label-caps tracking-widest text-accent-light uppercase">
                Kenya Medical Research Institute
            </span>
        </div>
    </div>

    <!-- flex-1 + justify-center: the card is centred in whatever height is left between band and footer -->
    <main class="w-full max-w-[1280px] flex-1 px-margin-mobile md:px-margin-desktop py-md flex flex-col items-center justify-center">
        <div
            class="w-full <?= $cardWidth ?> bg-surface-container-lowest border border-outline-variant border-t-4 border-t-primary px-md py-md md:px-lg shadow-md transition-all duration-300">
            <?= $this->render('_alerts') ?>
            <?= $content ?>
        </div>

        <div class="mt-sm text-center <?= $cardWidth ?> text-xs text-on-surface-variant">
            <span class="inline-flex items-center gap-xs">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse" aria-hidden="true"></span>
                <span class="font-semibold tracking-wide">All Institutional Systems Operational.</span>
            </span>
            <span class="opacity-70">Unauthorized access is prohibited; sessions are monitored under ICT Policies.</span>
        </div>
    </main>

    <div
        class="fixed top-1/4 -left-20 w-80 h-80 bg-accent opacity-20 blur-[100px] pointer-events-none rounded-full -z-10">
    </div>
    <div
        class="fixed bottom-1/4 -right-20 w-64 h-64 bg-primary-container opacity-20 blur-[100px] pointer-events-none rounded-full -z-10">
    </div>

    <footer
        class="w-full py-xs px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center gap-xs border-t border-outline-variant bg-surface-container-lowest">
        <div class="font-label-caps text-label-caps tracking-widest text-on-surface-variant text-center">
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