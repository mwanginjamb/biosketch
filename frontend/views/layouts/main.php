<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="light">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title ?: 'BioSketch Professional') ?></title>

    <?= Html::csrfMetaTags() ?>


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
<nav class="fixed top-0 w-full z-50 flex justify-between items-center px-4 md:px-12 h-16 bg-surface border-b border-outline-variant no-print">
    <div class="flex items-center gap-2">
        <?= Html::a(' <span class="material-symbols-outlined text-primary">biotech</span>
        BioSketch Professional',['site/index'],['class' => 'font-bold text-lg text-primary']) ?>
       
    </div>

    <div class="flex items-center gap-6">
        <div class="hidden md:flex gap-4">
            <a href="#" class="font-bold border-b-2 border-primary">Public Profile</a>
            <a href="<?=  Url::toRoute(['researcher/create']) ?>">Data Entry</a>
        </div>

        <img class="w-10 h-10 rounded-full border object-cover"
             src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmDS_35xhZTEhGVnpqDyladtXgq9lspOIeNlKI1td7_zTuY4HefWm4e2XVmxRIrc0qBhRGyxi9GpA5z6EkeVdFmUovQpTs9O1s_6qFYEBt70H30xC61FaiWuLVyzH34kcRvLki4XFykrV_gWEt0oGO7Qn5jqvD1LvWezwT5TInvH7ubXFVwo5hAzE_Rr8Rgl_Mi4GCCxRdRAJhA4kyZ2YjX5_w2YjfHgAioMJEvRJBVhTA14pmF6PLvnI65eFweLd3a_ZQg3AqOKah">
    </div>
</nav>


<!-- MAIN CONTENT -->
<main class="max-w-[1280px] mx-auto mt-16 px-4 md:px-12 py-10">
    <?= $content ?>
</main>


<!-- FOOTER -->
<footer class="w-full py-4 px-4 md:px-12 flex flex-col md:flex-row justify-between items-center mt-10 bg-gray-100 border-t no-print">
    <p class="text-sm text-gray-600 mb-2 md:mb-0">
        © <?= date('Y') ?> BioSketch Institutional. All rights reserved.
    </p>

    <div class="flex gap-6 text-sm">
        <a href="#">Compliance</a>
        <a href="#">Research Guidelines</a>
        <a href="#">Help Desk</a>
    </div>
</footer>


<!-- PRINT BUTTON -->
<button class="fixed bottom-4 right-4 w-12 h-12 bg-black text-white rounded-full shadow-lg flex items-center justify-center no-print"
        onclick="window.print()">
    <span class="material-symbols-outlined">print</span>
</button>


<!-- SCRIPT -->
<script>
const sections = document.querySelectorAll('.section-anchor');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100','translate-y-0');
            entry.target.classList.remove('opacity-50','translate-y-4');
        }
    });
}, { threshold: 0.3 });

sections.forEach(section => {
    section.classList.add('transition-all','duration-500','opacity-50','translate-y-4');
    observer.observe(section);
});
</script>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
