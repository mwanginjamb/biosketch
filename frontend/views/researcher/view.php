<?php
use yii\bootstrap5\Html;

 
?>

<!-- Main Content Wrapper -->
<div class="flex flex-col md:flex-row gap-lg">

    <!-- Sidebar Profile -->
    <aside class="w-full md:w-80 flex flex-col gap-md">

        <div class="bg-surface-container-lowest p-md border border-outline-variant rounded-lg flex flex-col items-center text-center">
            <img class="w-48 h-48 rounded-full mb-md object-cover border-4 border-surface"
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuBf29uY2KOEJUQSJ0BJA8WXHX4qbptUMAIuXD3Cqg7sjYrOTWYf4NtC0_UkketEA10SueZXgMgkwVoe3QjlsoNAu56aRI4zw8jQtrZCshJ8R3z1od8J01wQknNDDaM8S3zV6h0SJa3942MvYtGpKfXnRc3nMHWyZ6aSmGsrgYZHnB1YgAM5gwjTfWAyEdo8CmvJAorM0NA_xG1rOeSjklupBUGdJOeSSROl65b7s6oH2zrAF_dbu2IF5rjrxUVDOuZmz7ALybO57eJS">

            <h2 class="font-headline-lg text-headline-lg text-primary mb-base">
                <?= ucfirst($model->title).' '.ucwords($model->full_name)  ?>
            </h2>

            <p class="text-on-surface-variant mb-xs"><?= $model->role_title ?? 'N/A' ?></p>

            <div class="inline-flex items-center gap-xs px-sm py-xs bg-tertiary-fixed rounded-full mb-sm">
                <span class="material-symbols-outlined text-[16px]">account_balance</span>
                <span class="text-label-caps"><?= $model->department ?? 'N/A' ?></span>
            </div>

            <div class="w-full h-[1px] bg-outline-variant my-sm"></div>

            <div class="w-full flex flex-col gap-sm text-left">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined">mail</span>
                    <span class="font-data-mono"><?= $model->email ?? 'N/A' ?></span>
                </div>

                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined">language</span>
                    <span class="font-data-mono"><?= $model->website ?? 'N/A' ?></span>
                </div>

                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined">location_on</span>
                    <span class="font-data-mono"><?= $model->location ?? 'N/A' ?></span>
                </div>
            </div>
        </div>

        <!-- ORCID -->
        <div class="bg-surface-container-low p-md border border-outline-variant rounded-lg scientific-grid">
            <h3 class="text-label-caps text-on-surface-variant mb-sm">ORCID IDENTITY</h3>
            <p class="font-data-mono text-primary font-bold"><?= $model->orcid ?? 'N/A' ?></p>

            <div class="mt-md flex flex-wrap gap-xs">
                <span class="px-xs py-[2px] bg-secondary-container text-[11px] font-bold rounded">GENOMICS</span>
                <span class="px-xs py-[2px] bg-secondary-container text-[11px] font-bold rounded">CELL BIO</span>
                <span class="px-xs py-[2px] bg-secondary-container text-[11px] font-bold rounded">CRISPR</span>
            </div>
        </div>
    </aside>


    <!-- Main Content -->
    <div class="flex-1 flex flex-col gap-lg bg-surface-container-lowest p-md md:p-xl border border-outline-variant rounded-lg">

        <!-- Highlights -->
        <section class="section-anchor" id="highlights">
            <h3 class="font-headline-md uppercase mb-md">Research Highlights</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">

                <div class="bg-surface p-sm border rounded">
                    <h4 class="text-secondary mb-xs">Major Breakthrough</h4>
                    <p class="font-semibold">Pathway-01 Discovery</p>
                    <p>Identified novel signaling pathways in mitochondrial DNA repair.</p>
                </div>

                <div class="bg-surface p-sm border rounded">
                    <h4 class="text-secondary mb-xs">Patent Filed</h4>
                    <p class="font-semibold">Synthetix-V Gene Drive</p>
                    <p>High-precision gene editing with 99.8% accuracy.</p>
                </div>

            </div>
        </section>


        <!-- Academic -->
        <section class="section-anchor" id="academic">
            <h3 class="font-headline-md uppercase mb-md">Academic Background</h3>

            <div class="space-y-md">
                <?php foreach ($model->researcherEducations as $education): ?>
                    <div class="flex flex-col gap-sm">
                        <div class="flex gap-md pb-sm border-b border-surface-container">
                            <div class="font-data-mono text-data-mono text-on-surface-variant min-w-[80px]"><?= $education->graduation_year ?? 'N/A' ?></div>
                            <div>
                                <p class="font-body-lg text-body-lg font-bold text-primary"><?=  $education->degree ?? 'N/A' ?> </p>
                                <p class="font-body-md text-on-surface-variant"><?= $education->institution_name ?? 'N/A'  ?> </p>
                                <p class="font-body-md text-on-surface-variant italic mt-xs"><?= $education->field_of_study ?? 'N/A'  ?></p>
                            </div>
                        </div>
                <?php endforeach; ?>

            </div>
        </section>


        <!-- Publications -->
        <section class="section-anchor" id="publications">
            <div class="flex items-center gap-xs mb-md border-b border-outline-variant pb-xs">
            <span class="material-symbols-outlined text-primary" data-icon="description">description</span>
            <h3 class="font-headline-md text-headline-md uppercase tracking-tight">Selected Publications</h3>
            </div>

            <div class="space-y-md">

            <?php foreach($model->publications as $pub): ?>

               <div class="p-sm hover:bg-surface transition-colors border-l-4 border-primary">
                <p class="font-body-md text-on-surface mb-xs leading-relaxed">
                    <span class="font-bold"><?= $pub->journal ?></span> (<?= $pub->publication_year ?? 'N/A' ?>) <span class="italic"><?= $pub->title?? 'N/A' ?></span>.
                </p>
                <div class="flex gap-sm items-center">
                    <span class="font-label-caps text-label-caps text-secondary"><?= Html::a(Html::encode($pub->doi??'#'),$pub->doi) ?></span>
                  
                </div>
            </div>

                <?php endforeach; ?>

                

            </div>
        </section>


        <!-- Grants -->
        <section class="section-anchor" id="grants">
            <div class="flex items-center gap-xs mb-md border-b border-outline-variant pb-xs">
                <span class="material-symbols-outlined text-primary" data-icon="monetization_on">monetization_on</span>
                <h3 class="font-headline-md text-headline-md uppercase tracking-tight">Ongoing Grants</h3>
            </div>

            <div class="space-y-sm">

                <div class="bg-surface-container-low p-sm rounded border border-outline-variant flex justify-between items-start">
                    <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">NIH R01 GM123456</p>
                    <p class="font-body-md font-bold text-primary">Mechanisms of CRISPR-Cas9 Target Selection</p>
                    <p class="font-body-md text-on-surface-variant">Role: Principal Investigator | $1.2M Total Award</p>
                    </div>
                    <div class="px-xs py-[2px] bg-secondary text-on-secondary rounded font-bold text-[10px]">ACTIVE</div>
                </div>

                

            </div>
        </section>

    </div>
</div>
