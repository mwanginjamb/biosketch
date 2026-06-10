<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="publication-container" class="space-y-sm">
    <div class="publication-item" data-index="<?= $index ?>">
        <div class="flex items-center justify-between mb-2">

            <button type="button"
                class="remove-publication flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                ✕
            </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group space-y-xs">
            <!-- <div class="flex items-center justify-between mb-2">
                    <span class="font-label-caps text-[10px] text-secondary font-bold">SELECTED FOR PROFILE</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked="">
                        <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
                    </label>
                </div> -->
            <div class="grid grid-cols-1 gap-xs">
                <div>
                    <?= $form->field($model, 'title', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'Neural Circuitry of Circadian Rhythms in D. melanogaster']) ?>
                </div>
                <div class="grid grid-cols-3 gap-xs">
                    <div class="col-span-2">
                        <?= $form->field($model, 'journal', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'Journal / Venue']) ?>
                    </div>
                    <div>
                        <?= $form->field($model, 'publication_year', FormUi::fieldConfig())->textInput(['type' => 'number', 'pattern' => '[0-9]{4}', 'class' => FormUi::inputClassMono(), 'placeholder' => 'Publication Year']) ?>
                    </div>
                </div>
                <div>
                    <?= $form->field($model, 'doi', FormUi::fieldConfig())->textInput(['type' => 'url', 'class' => FormUi::inputClassMono(), 'placeholder' => 'DOI - Digital Object Identifier']) ?>
                </div>
                <div>
                    <?= $form->field($model, 'pmid', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'PMID - PubMed Identifier']) ?>
                </div>
            </div>
        </div>
    </div>

</div>