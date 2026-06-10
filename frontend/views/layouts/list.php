<?php
use yii\bootstrap5\Html;

/* @var $this \yii\web\View */
/* @var $content string */
use frontend\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="light" lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= Html::encode($this->title ?: ' Biosketches List') ?></title>
    <?= Html::csrfMetaTags() ?>

    <?php $this->head() ?>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-surface text-on-surface flex flex-col min-h-screen">
<?php $this->beginBody() ?>

<!-- TopNavBar -->
<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-gutter h-16 bg-surface-container-lowest border-b border-outline-variant">
    <div class="flex items-center gap-md">
        <span class="font-headline-md text-headline-md font-bold text-primary">BioSketch Pro</span>
        <nav class="hidden md:flex gap-md ml-lg">
           
            <a class="text-secondary font-bold border-b-2 border-secondary px-xs py-xs" href="#">List</a>
            
        </nav>
    </div>
</header>

<!-- SideNavBar -->
<aside class="fixed left-0 top-16 h-[calc(100vh-64px)] w-64 flex flex-col p-sm z-40 bg-surface border-r border-outline-variant hidden md:flex">
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-xs px-sm py-xs my-1 text-on-surface-variant" href="#">
            <span class="material-symbols-outlined">edit_note</span>
            <span class="font-body-md">Data Entry</span>
        </a>
        <a class="flex items-center gap-xs px-sm py-xs my-1 transition-all duration-200 bg-secondary-container text-on-secondary-container rounded-xl font-semibold" href="#">
            <span class="material-symbols-outlined">insights</span>
            <span class="font-body-md">Analytics</span>
        </a>
    </nav>

<div class="mt-auto space-y-1 border-t border-outline-variant pt-sm">
    
    <?= Html::a('<span class="material-symbols-outlined">Power</span> Sign out',['site/logout'],['class' => 'flex items-center gap-xs px-sm py-xs transition-all text-on-surface-variant hover:bg-surface-container-high rounded-xl' ]) ?>
      
</div>
</aside>

<!-- Main Content Area -->
<main class="md:ml-64 mt-16 flex-1 flex flex-col min-h-[calc(100vh-64px)]">
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>