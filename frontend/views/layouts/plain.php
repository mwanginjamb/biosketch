<?php
/** @var yii\web\View $this */
$this->render('_head');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>

<html class="h-full" lang="en">

<head>
    <?php $this->head() ?>
    <title>Welcome - Researcher BioSketch Pro</title>
</head>

<body class="bg-surface text-on-surface font-body-md min-h-screen flex flex-col pt-16">
    <?php $this->beginBody() ?>
    <!-- TopNavBar (Rendered from JSON structure) -->
    <header
        class="bg-surface-container-lowest border-b border-outline-variant fixed top-0 left-0 w-full z-50 flex justify-between items-center px-gutter h-16">
        <div class="flex items-center gap-xs">
            <span class="font-headline-md text-headline-md font-bold text-primary">BioSketch Pro</span>
        </div>
        <!-- Navigation removed as this is a destination landing page for logged in user starting their journey -->
        <div class="flex items-center gap-md">
            <span
                class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-surface-container-low transition-colors"
                data-icon="notifications">notifications</span>
            <!-- logout button -->

            <?= Html::a('<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-surface-container-low transition-colors">logout</span>', ['site/logout'], [
                'class' => '',
                'data' => [
                    'method' => 'post',
                ],
            ]) ?>


            <div
                class="h-8 w-8 rounded-full bg-surface-container-high border border-outline-variant overflow-hidden cursor-pointer flex items-center justify-center">
                <img alt="Scientist profile"
                    src="<?= (!\Yii::$app->user->isGuest && Yii::$app->user->identity?->researcher) ? Yii::$app->user->identity->researcher->profile_photo : 'https://via.placeholder.com/150' ?>">
            </div>
        </div>
    </header>
    <!-- Main Content Area -->
    <main
        class="flex-grow flex flex-col items-center justify-center px-margin-mobile md:px-margin-desktop py-xl bg-surface">
        <?= $content ?>
    </main>
    <!-- Footer (Rendered from JSON structure) -->
    <footer
        class="w-full mt-auto py-lg px-gutter flex flex-col md:flex-row justify-between items-center bg-surface-container-low border-t border-outline-variant">
        <div class="font-label-caps text-label-caps text-on-surface-variant">
            © <?= date('Y') . ' ' . Yii::$app->name ?> . All rights reserved.
        </div>
        <!-- <div class="flex gap-md mt-md md:mt-0 font-label-caps text-label-caps">

        <a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Compliance
                Policy</a>
            <a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Research
                Guidelines</a>
            <a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Privacy
                Protocol</a>
            <a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">IRB
                Documentation</a>
        </div> -->
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>