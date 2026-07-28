<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);

$this->registerCsrfMetaTags();
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="light">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= Html::encode($this->title) ?></title>


    <?php $this->head() ?>


</head>

<body class="bg-surface text-on-surface font-body-md">

    <?php $this->beginBody() ?>

    <!-- NAVBAR -->
    <nav
        class="fixed top-0 w-full z-50 flex justify-between items-center px-4 md:px-12 h-16 max-w-[1280px] mx-auto bg-white border-b border-gray-200 no-print">

        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined">biotech</span>
            <h1 class="font-bold text-xl">
                BioSketch Professional
            </h1>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:flex gap-4">
                <?= Html::a('Public Profile', ['researcher/view'], [
                    'class' => 'font-bold border-b-2 border-black'
                ]) ?>

                <?= Html::a('Data Entry', ['researcher/update']) ?>
            </div>
            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

    </nav>

    <!-- MAIN CONTENT -->
    <main
        class="max-w-[1280px] mx-auto mt-16 px-margin-mobile md:px-margin-desktop py-lg flex flex-col md:flex-row gap-lg">

        <?= $content ?>

    </main>

    <!-- FOOTER -->
    <footer
        class="w-full py-4 px-4 md:px-12 flex flex-col md:flex-row justify-between items-center mt-10 bg-white border-t border-gray-200 no-print">

        <p class="text-xs uppercase tracking-wider text-gray-500">
            © <?= date('Y') ?> BioSketch Institutional. All rights reserved.
        </p>

        <div class="flex gap-6 mt-3 md:mt-0">
            <a href="#">Compliance</a>
            <a href="#">Research Guidelines</a>
            <a href="#">Help Desk</a>
        </div>

    </footer>

    <!-- PRINT BUTTON -->
    <button
        class="fixed bottom-4 right-4 w-12 h-12 bg-black text-white rounded-full shadow-lg flex items-center justify-center no-print"
        onclick="window.print()">

        <span class="material-symbols-outlined">print</span>

    </button>

    <?php
    $this->registerJs(<<<JS
const sections = document.querySelectorAll('.section-anchor');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.classList.add('opacity-100','translate-y-0');
            entry.target.classList.remove('opacity-50','translate-y-4');
        }
    });
},{
    threshold:0.3
});

sections.forEach(section=>{
    section.classList.add(
        'transition-all',
        'duration-500',
        'opacity-50',
        'translate-y-4'
    );
    observer.observe(section);
});
JS);
    ?>

    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>