<?php
use yii\bootstrap5\Html;
use yii\helpers\Url;
$this->render('_head');

$placeholderText = !empty(Yii::$app->user->identity->username) ? \Yii::$app->user->identity->username : 'User';
$placeholderImage = "https://placehold.co/150/cccccc/FFFFFF.webp/?text=" . rawurlencode($placeholderText);
$profilePhoto = (!\Yii::$app->user->isGuest ? Yii::$app->user->identity?->researcher?->profile_photo : null) ?: $placeholderImage;

$this->beginPage();
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="light">

<head>
    <title><?= Html::encode($this->title ?: Yii::$app->name) ?></title>
    <style>
        body {
            min-height: max(884px, 100dvh);
        }
    </style>

    <?php $this->head(); ?>
</head>

<body class="bg-surface text-on-surface font-body-md">
    <?php $this->beginBody(); ?>

    <!-- NAVBAR -->
    <nav
        class="fixed top-0 w-full z-50 flex justify-between items-center px-4 md:px-12 h-16 bg-primary text-on-primary border-b-4 border-accent shadow-md no-print">
        <div class="flex items-center gap-2">
            <?= Html::a(' <span class="material-symbols-outlined text-accent-light" aria-hidden="true">biotech</span>
        BioSketch Professional', ['site/index'], ['class' => 'font-bold text-lg text-on-primary flex items-center gap-xs']) ?>

        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:flex gap-4">
                <?php if (!\Yii::$app->user->isGuest && Yii::$app->user->identity->researcher): ?>
                    <a href="<?= Url::toRoute(Yii::$app->user->identity->researcher->getProfileRoute()) ?>"
                        class="font-bold border-b-2 border-accent pb-0.5">Public Profile</a>
                    <a class="text-on-primary/90 hover:text-accent-light transition-colors" href="<?= Url::toRoute(['researcher/update', 'id' => Yii::$app->user->identity->researcher->id]) ?>">Update
                        BioSketch</a>
                <?php endif; ?>
            </div>
            <?= $this->render('_notifications') ?>
            <?= $this->render('_account_menu', ['profilePhoto' => $profilePhoto, 'sizeClass' => 'w-12 h-12']) ?>

        </div>
    </nav>


    <!-- MAIN CONTENT -->
    <main class="max-w-[1280px] mx-auto mt-16 px-4 md:px-12 py-10">
        <?= $content ?>
    </main>


    <!-- FOOTER -->
    <footer
        class="w-full py-4 px-4 md:px-12 flex flex-col md:flex-row justify-between items-center mt-10 bg-primary text-on-primary border-t-4 border-accent no-print">
        <p class="text-sm text-on-primary/80 mb-2 md:mb-0">
            © <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. All rights reserved.
        </p>
        <p class="font-label-caps text-label-caps tracking-widest uppercase text-accent-light">Kenya Medical Research Institute</p>

        <!-- <div class="flex gap-6 text-sm">
            <a href="#">Compliance</a>
            <a href="#">Research Guidelines</a>
            <a href="#">Help Desk</a>
        </div> -->
    </footer>


    <!-- PRINT BUTTON -->
    <button
        class="fixed bottom-4 right-4 w-12 h-12 bg-primary text-on-primary hover:bg-primary-container border-2 border-accent rounded-full shadow-lg flex items-center justify-center no-print" title="Print" aria-label="Print"
        onclick="window.print()">
        <span class="material-symbols-outlined">print</span>
    </button>



    <?php $this->endBody(); ?>
</body>

</html>
<?php $this->endPage(); ?>