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
<body class="bg-surface text-on-surface font-body-md selection:bg-secondary-container selection:text-on-secondary-container">
<?php $this->beginBody(); ?>

<nav class="fixed top-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-margin-desktop h-16 max-w-[1280px] mx-auto bg-surface border-b border-outline-variant no-print">
    <div class="flex items-center gap-xs">
        <span class="material-symbols-outlined text-primary" data-icon="biotech">biotech</span>
        <h1 class="font-headline-md text-headline-md font-bold text-primary">BioSketch Professional</h1>
    </div>
    <div class="flex items-center gap-md">
        <div class="hidden md:flex gap-sm">
            <a class="text-primary font-bold border-b-2 border-primary cursor-pointer" href="#">Public Profile</a>
            <a class="text-on-surface-variant hover:text-secondary transition-colors" href="#">Data Entry</a>
        </div>
        <img alt="Scientist Profile" class="w-10 h-10 rounded-full border border-outline-variant object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmDS_35xhZTEhGVnpqDyladtXgq9lspOIeNlKI1td7_zTuY4HefWm4e2XVmxRIrc0qBhRGyxi9GpA5z6EkeVdFmUovQpTs9O1s_6qFYEBt70H30xC61FaiWuLVyzH34kcRvLki4XFykrV_gWEt0oGO7Qn5jqvD1LvWezwT5TInvH7ubXFVwo5hAzE_Rr8Rgl_Mi4GCCxRdRAJhA4kyZ2YjX5_w2YjfHgAioMJEvRJBVhTA14pmF6PLvnI65eFweLd3a_ZQg3AqOKah">
    </div>
</nav>

<main class="max-w-[1280px] mx-auto mt-16 px-margin-mobile md:px-margin-desktop py-lg flex flex-col md:flex-row gap-lg">
    <aside class="w-full md:w-80 flex flex-col gap-md">
        <!-- Sidebar content identical to original design -->
        <div class="bg-surface-container-lowest p-md border border-outline-variant rounded-lg flex flex-col items-center text-center">
            <img alt="Dr. Alex Rivera" class="w-48 h-48 rounded-full mb-md object-cover border-4 border-surface" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBf29uY2KOEJUQSJ0BJA8WXHX4qbptUMAIuXD3Cqg7sjYrOTWYf4NtC0_UkketEA10SueZXgMgkwVoe3QjlsoNAu56aRI4zw8jQtrZCshJ8R3z1od8J01wQknNDDaM8S3zV6h0SJa3942MvYtGpKfXnRc3nMHWyZ6aSmGsrgYZHnB1YgAM5gwjTfWAyEdo8CmvJAorM0NA_xG1rOeSjklupBUGdJOeSSROl65b7s6oH2zrAF_dbu2IF5rjrxUVDOuZmz7ALybO57eJS">
            <h2 class="font-headline-lg text-headline-lg text-primary mb-base">Dr. Alex Rivera</h2>
            <p class="font-body-md text-on-surface-variant mb-xs">Principal Investigator</p>
            <div class="inline-flex items-center gap-xs px-sm py-xs bg-tertiary-fixed text-on-tertiary-fixed rounded-full mb-sm">
                <span class="material-symbols-outlined text-[16px]" data-icon="account_balance">account_balance</span>
                <span class="font-label-caps text-label-caps">Department of Biology</span>
            </div>
            <div class="w-full h-[1px] bg-outline-variant my-sm"></div>
            <div class="w-full flex flex-col gap-sm text-left">
                <div class="flex items-center gap-sm"><span class="material-symbols-outlined text-primary">mail</span><span class="font-data-mono text-data-mono">a.rivera@inst.edu</span></div>
                <div class="flex items-center gap-sm"><span class="material-symbols-outlined text-primary">language</span><span class="font-data-mono text-data-mono">riveralab.org</span></div>
                <div class="flex items-center gap-sm"><span class="material-symbols-outlined text-primary">location_on</span><span class="font-data-mono text-data-mono">Cambridge, MA</span></div>
            </div>
        </div>
        <div class="bg-surface-container-low p-md border border-outline-variant rounded-lg scientific-grid">
            <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-sm">ORCID IDENTITY</h3>
            <p class="font-data-mono text-data-mono text-primary font-bold">0000-0002-1825-0097</p>
            <div class="mt-md flex flex-wrap gap-xs">
                <span class="px-xs py-[2px] bg-secondary-container text-on-secondary-container text-[11px] font-bold rounded">GENOMICS</span>
                <span class="px-xs py-[2px] bg-secondary-container text-on-secondary-container text-[11px] font-bold rounded">CELL BIO</span>
                <span class="px-xs py-[2px] bg-secondary-container text-on-secondary-container text-[11px] font-bold rounded">CRISPR</span>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col gap-lg bg-surface-container-lowest p-md md:p-xl border border-outline-variant rounded-lg">
        <?= $content ?>
    </div>
</main>

<footer class="w-full py-sm px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center mt-lg bg-surface-container-lowest border-t border-outline-variant no-print">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-md md:mb-0">© 2024 BioSketch Institutional. All rights reserved. Clinical Grade Precision.</p>
    <div class="flex gap-md">
        <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-primary transition-colors" href="#">Compliance</a>
        <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-primary transition-colors" href="#">Research Guidelines</a>
        <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-primary transition-colors" href="#">Help Desk</a>
    </div>
</footer>

<button class="fixed bottom-margin-mobile right-margin-mobile w-12 h-12 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:opacity-90 active:scale-95 transition-all no-print" onclick="window.print()">
    <span class="material-symbols-outlined" data-icon="print">print</span>
</button>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>