<?php
use yii\bootstrap5\Html;


//exit(Yii::getAlias('@frontend/web') . $model->profile_photo);
?>

<!-- Main Content Wrapper -->
<div class="flex flex-col md:flex-row gap-lg">

    <!-- Sidebar Profile -->
    <aside class="w-full md:w-80 flex flex-col gap-md">

        <div
            class="bg-surface-container-lowest p-md border border-outline-variant rounded-lg flex flex-col items-center text-center">
            <img class="w-48 h-48 rounded-full mb-md object-cover border-4 border-surface"
                src="<?= $model->profile_photo ? $model->profile_photo : 'https://via.placeholder.com/150' ?>">

            <h2 class="font-headline-lg text-headline-lg text-primary mb-base">
                <?= ucfirst($model->title) . ' ' . ucwords($model->full_name) ?>
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
                    <span class="font-data-mono">
                        <?=
                            // remove scheme (http:// or https://)  and "www" from website if present
                            str_replace(['http://', 'https://', 'www.'], '', $model->website) ?? 'N/A'
                            ?>
                    </span>
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
                <!-- Get research tags as array of tags -->
                <?php
                $tags = explode(',', $model->research_tags);
                if (!is_array($tags)) {
                    echo '<span class="px-xs py-[2px] bg-secondary-container text-[11px] font-bold rounded">No tags available</span>';

                }
                foreach ($tags as $tag) {
                    echo '<span class="px-xs py-[2px] bg-secondary-container text-[11px] font-bold rounded">' . trim($tag) . '</span>';
                }
                ?>

            </div>
        </div>
    </aside>


    <!-- Main Content -->
    <div
        class="flex-1 flex flex-col gap-lg bg-surface-container-lowest p-md md:p-xl border border-outline-variant rounded-lg">

        <!-- Highlights -->
        <section class="section-anchor" id="highlights">

            <!-- Add a div, within it there is a link to view the biosketch pdf report, link should be to the furthest right -->
            <div class="flex justify-between mb-sm">
                <div> </div>
                <?= Html::a('View BioSketch PDF', ['researcher/report', 'id' => $model->id], ['class' => 'px-md py-xs bg-primary text-on-primary rounded hover:bg-primary-container transition-all']) ?>
            </div>



            <h3 class="font-headline-md text-headline-md uppercase tracking-tight">Research Highlights</h3>



            <h3 class="font-headline-md uppercase mb-md">Research Highlights</h3>



            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">

                <div class="bg-surface p-sm border rounded">
                    <h4 class="text-secondary mb-xs">Major Breakthrough</h4>
                    <!-- <p class="font-semibold">Pathway-01 Discovery</p> -->
                    <p><?= ucfirst($model->major_breakthrough) ?? '' ?></p>
                </div>

                <div class="bg-surface p-sm border rounded">
                    <h4 class="text-secondary mb-xs">Patent Filed</h4>
                    <!-- <p class="font-semibold">Synthetix-V Gene Drive</p> -->
                    <p><?= $model->patent_filed ?? '' ?></p>
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
                            <div class="font-data-mono text-data-mono text-on-surface-variant min-w-[80px]">
                                <?= $education->graduation_year ?? 'N/A' ?>
                            </div>
                            <div>
                                <p class="font-body-lg text-body-lg font-bold text-primary">
                                    <?= $education->degree ?? 'N/A' ?>
                                </p>
                                <p class="font-body-md text-on-surface-variant">
                                    <?= $education->institution_name ?? 'N/A' ?>
                                </p>
                                <p class="font-body-md text-on-surface-variant italic mt-xs">
                                    <?= $education->field_of_study ?? 'N/A' ?>
                                </p>
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

                <?php foreach ($model->publications as $pub): ?>

                    <div class="p-sm hover:bg-surface transition-colors border-l-4 border-primary">
                        <p class="font-body-md text-on-surface mb-xs leading-relaxed">
                            <span class="font-bold"><?= $pub->journal ?></span> (<?= $pub->publication_year ?? 'N/A' ?>)
                            <span class="italic"><?= $pub->title ?? 'N/A' ?></span>.
                        </p>
                        <div class="flex gap-sm items-center">
                            <span
                                class="font-label-caps text-label-caps text-secondary"><?= Html::a(Html::encode($pub->doi ?? '#'), $pub->doi) ?></span>

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


                <?php foreach ($model->researcherGrants as $grant): ?>

                    <div
                        class="bg-surface-container-low p-sm rounded border border-outline-variant flex justify-between items-start">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">
                                <?= Html::encode($grant->grant_number ?? 'N/A') ?>
                            </p>
                            <p class="font-body-md font-bold text-primary">
                                <?= Html::encode($grant->title ?? 'N/A') ?>
                            </p>
                            <p class="font-body-md text-on-surface-variant">Role:
                                <?= Html::encode($grant->roleOptions[$grant->role_id] ?? 'N/A') ?> |
                                <?= Html::encode(Yii::$app->formatter->asCurrency($grant->amount, $grant->currency) ?? 'N/A') ?>
                            </p>
                        </div>
                        <div class="px-xs py-[2px] bg-secondary text-on-secondary rounded font-bold text-[10px]">
                            <?= $grant->statusOptions[$grant->status_id] ?? 'N/A' ?>
                        </div>
                    </div>

                <?php endforeach; ?>



            </div>
        </section>

    </div>
</div>