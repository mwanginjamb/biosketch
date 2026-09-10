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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-surface text-on-surface">
    <?php $this->beginBody(); ?>

    <header
        class="fixed top-0 w-full z-50 bg-surface flex justify-between items-center px-margin-mobile h-16 border-b border-outline-variant">
        <div class="flex items-center gap-sm">
            <?= Html::a(' <span class="material-symbols-outlined text-primary">biotech</span>
        BioSketch Professional', ['site/index'], ['class' => 'font-bold text-lg text-primary']) ?>
        </div>
        <div class="w-12 h-12 rounded-full overflow-hidden border border-outline-variant">
            <img alt="Scientist profile"
                src="<?= (!\Yii::$app->user->isGuest && Yii::$app->user->identity?->researcher) ? Yii::$app->user->identity->researcher->profile_photo : 'https://via.placeholder.com/150' ?>">
        </div>
    </header>

    <main class="pt-20 pb-xl px-margin-mobile max-w-2xl mx-auto space-y-md">
        <?= $content ?>
    </main>

    <nav
        class="fixed bottom-0 w-full bg-surface-container-lowest border-t border-outline-variant flex justify-around items-center h-16 md:hidden z-50">
        <div class="flex flex-col items-center text-secondary font-bold">
            <span class="material-symbols-outlined" data-icon="edit_note"
                style="font-variation-settings: 'FILL' 1;">edit_note</span>
            <span class="text-[10px] mt-0.5">EDIT</span>
        </div>
        <div class="flex flex-col items-center text-on-surface-variant">
            <span class="material-symbols-outlined" data-icon="description">description</span>
            <span class="text-[10px] mt-0.5">PROFILE</span>
        </div>
        <div class="flex flex-col items-center text-on-surface-variant">
            <span class="material-symbols-outlined" data-icon="analytics">analytics</span>
            <span class="text-[10px] mt-0.5">METRICS</span>
        </div>
        <div class="flex flex-col items-center text-on-surface-variant">
            <span class="material-symbols-outlined" data-icon="settings">settings</span>
            <span class="text-[10px] mt-0.5">SETUP</span>
        </div>
    </nav>

    <footer
        class="hidden md:block w-full py-sm px-margin-desktop bg-surface-container-lowest border-t border-outline-variant mt-lg">
        <div class="max-w-[1280px] mx-auto flex justify-between items-center">
            <p class="font-label-caps text-label-caps text-on-surface-variant">© 2024 BioSketch Institutional. All
                rights reserved. Clinical Grade Precision.</p>
            <div class="flex gap-md">
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                    href="#">Compliance</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                    href="#">Research Guidelines</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors"
                    href="#">Help Desk</a>
            </div>
        </div>
    </footer>

    <?php $this->endBody(); ?>
</body>

</html>
<?php $this->endPage(); ?>