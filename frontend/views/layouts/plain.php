<?php
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
<header class="bg-surface-container-lowest border-b border-outline-variant fixed top-0 left-0 w-full z-50 flex justify-between items-center px-gutter h-16">
<div class="flex items-center gap-xs">
<span class="font-headline-md text-headline-md font-bold text-primary">BioSketch Pro</span>
</div>
<!-- Navigation removed as this is a destination landing page for logged in user starting their journey -->
<div class="flex items-center gap-md">
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-surface-container-low transition-colors" data-icon="notifications">notifications</span>
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-surface-container-low transition-colors" data-icon="help_outline">help_outline</span>
<div class="h-8 w-8 rounded-full bg-surface-container-high border border-outline-variant overflow-hidden cursor-pointer flex items-center justify-center">
<img alt="Researcher Profile" class="w-full h-full object-cover" data-alt="A small, professional headshot photo of a clinical researcher, well-lit against a neutral background. The person looks approachable and intelligent. High resolution, corporate modern aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFDUYRMuiy9EhrRuinw_5rPr3CcLVBFuU4doGSMfmgIvK_fwsgCnk7gbYWobDWla2-OZlZWRaMH0abrUlTzwym4uE8KC2kxPqVr-vpSX4Ig4iwvJodzfA8nzLOrGxJm0v3CqlYD63z43Ihwu9edPSi0Oecv5SrQptXWgMAs3wsDTD2G17NNLFXXdqblmcoxIm_op_DjZE9Cc-Wp6kCd15-C8njRXCAozcw_NkXpAITOPzD7kAKJsH9xg"/>
</div>
</div>
</header>
<!-- Main Content Area -->
<main class="flex-grow flex flex-col items-center justify-center px-margin-mobile md:px-margin-desktop py-xl bg-surface">
    <?= $content ?>
</main>
<!-- Footer (Rendered from JSON structure) -->
<footer class="w-full mt-auto py-lg px-gutter flex flex-col md:flex-row justify-between items-center bg-surface-container-low border-t border-outline-variant">
<div class="font-label-caps text-label-caps text-on-surface-variant">
            © <?= date('Y')  ?> ?>  Clinical Research Systems. All rights reserved.
        </div>
<div class="flex gap-md mt-md md:mt-0 font-label-caps text-label-caps">
<a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Compliance Policy</a>
<a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Research Guidelines</a>
<a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">Privacy Protocol</a>
<a class="text-on-surface-variant opacity-80 hover:text-primary transition-colors" href="#">IRB Documentation</a>
</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>