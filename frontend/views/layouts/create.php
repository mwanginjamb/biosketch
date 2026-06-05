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
    <?php $this->head() ?>
</head>
<body class="bg-surface text-on-surface">
<?php $this->beginBody(); ?>

<header class="fixed top-0 w-full z-50 bg-surface flex justify-between items-center px-margin-mobile h-16 border-b border-outline-variant">
    <div class="flex items-center gap-sm">
        <span class="material-symbols-outlined text-primary" data-icon="biotech">biotech</span>
        <h1 class="font-headline-md text-headline-md font-bold text-primary">BioSketch Professional</h1>
    </div>
    <div class="w-8 h-8 rounded-full overflow-hidden border border-outline-variant">
        <img alt="Scientist profile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCeG1TXImo-Swo3V1Q7R_EmIEiEi2vYXzxIrJeGyBExyUi8WfGAwBZzT1NUafKFGUKz_qI81a5YhSIKWBbdLW50jLq7Crctsb0Z4IJd2HfX9-7yTdbfunxHL06MgBz7u7dcxdj14KD19w97GnVngP8tgm-6m4Tjun7u38maysRMSB66CJtcVUsamcT8ys0KoqJDIrPF1ant4RPHtQ8o16xbVqW-vWCTYvow8V0rAwJOF-nqtKgKrHPlBpJLkvrDnrmUCU9gW-sZrKT2">
    </div>
</header>

<main class="pt-20 pb-xl px-margin-mobile max-w-2xl mx-auto space-y-md">
    <?= $content ?>
</main>

<nav class="fixed bottom-0 w-full bg-surface-container-lowest border-t border-outline-variant flex justify-around items-center h-16 md:hidden z-50">
    <div class="flex flex-col items-center text-secondary font-bold">
        <span class="material-symbols-outlined" data-icon="edit_note" style="font-variation-settings: 'FILL' 1;">edit_note</span>
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

<footer class="hidden md:block w-full py-sm px-margin-desktop bg-surface-container-lowest border-t border-outline-variant mt-lg">
    <div class="max-w-[1280px] mx-auto flex justify-between items-center">
        <p class="font-label-caps text-label-caps text-on-surface-variant">© 2024 BioSketch Institutional. All rights reserved. Clinical Grade Precision.</p>
        <div class="flex gap-md">
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="#">Compliance</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="#">Research Guidelines</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="#">Help Desk</a>
        </div>
    </div>
</footer>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>