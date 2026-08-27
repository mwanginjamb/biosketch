<?php
use yii\helpers\Url;
?>
<div class="max-w-3xl w-full text-center space-y-lg">
    <div class="space-y-sm">
        <h1 class="font-display text-display text-primary">Welcome to BioSketch Pro</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mx-auto">
            The streamlined platform for researchers and medical professionals to build, manage, and export rigorous
            clinical biosketches.
        </p>
    </div>
    <div
        class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col md:flex-row gap-md items-center justify-center max-w-2xl mx-auto">
        <button
            class="w-full md:w-auto flex-1 flex flex-col items-center justify-center gap-sm p-md rounded-xl border-2 border-secondary bg-surface-container-lowest hover:bg-secondary-container text-secondary transition-all group">
            <span class="material-symbols-outlined text-4xl group-hover:scale-110 transition-transform"
                data-icon="play_circle">play_circle</span>
            <div class="text-center">
                <span class="font-headline-md text-headline-md block">Watch Tutorial</span>
                <span class="font-body-md text-body-md opacity-80 mt-1 block">Learn the workflow (3 min)</span>
            </div>
        </button>

        <!-- researcher without a profile: create -->

        <?php if (!\Yii::$app->user->isGuest && !Yii::$app->user->identity->researcher): ?>

            <a href="<?= Url::toRoute(['researcher/create']) ?>"
                class="w-full md:w-auto flex-1 flex flex-col items-center justify-center gap-sm p-md rounded-xl bg-primary text-on-primary hover:bg-on-surface-variant transition-all group shadow-md hover:shadow-lg">
                <span class="material-symbols-outlined text-4xl group-hover:scale-110 transition-transform"
                    data-icon="add_document">post_add</span>
                <div class="text-center">
                    <span class="font-headline-md text-headline-md block">Create Your BioSketch</span>
                    <span class="font-body-md text-body-md opacity-80 mt-1 block">Start building your profile</span>
                </div>
            </a>

        <?php endif; ?>

        <!-- researcher with a profile: view/edit -->
        <?php if (!\Yii::$app->user->isGuest && Yii::$app->user->identity->researcher): ?>

            <a href="<?= Url::toRoute(['researcher/view', 'id' => Yii::$app->user->identity->researcher->id]) ?>"
                class="w-full md:w-auto flex-1 flex flex-col items-center justify-center gap-sm p-md rounded-xl bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low transition-all group shadow-md hover:shadow-lg">
                <span class="material-symbols-outlined text-4xl group-hover:scale-110 transition-transform"
                    data-icon="person">person</span>
                <div class="text-center">
                    <span class="font-headline-md text-headline-md block">View Your BioSketch</span>
                    <span class="font-body-md text-body-md opacity-80 mt-1 block">Manage your existing profile</span>
                </div>
            </a>

        <?php endif; ?>





    </div>
</div>