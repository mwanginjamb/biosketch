<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="education-template" class="hidden space-y-sm">

    <div class="education-item">
        <div class="flex items-center justify-between mb-2">

                    <button type="button"
                        class="remove-source flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                        ✕
                    </button>

        </div>

            <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group space-y-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-label-caps text-[10px] text-secondary font-bold">SELECTED FOR PROFILE</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked="">
                        <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
                    </label>
                </div>
                <div class="grid grid-cols-1 gap-xs">
                    <div>
                        <label class="font-label-caps text-[10px] text-on-surface-variant block mb-1">PUBLICATION TITLE</label>
                        <input class="w-full bg-white border border-outline-variant rounded p-2 text-body-md font-body-md form-focus-ring" type="text" value="Neural Circuitry of Circadian Rhythms in D. melanogaster">
                    </div>
                    <div class="grid grid-cols-3 gap-xs">
                        <div class="col-span-2">
                            <label class="font-label-caps text-[10px] text-on-surface-variant block mb-1">JOURNAL / VENUE</label>
                            <input class="w-full bg-white border border-outline-variant rounded p-2 text-body-md font-body-md form-focus-ring" type="text" value="Nature Neuroscience">
                        </div>
                        <div>
                            <label class="font-label-caps text-[10px] text-on-surface-variant block mb-1">YEAR</label>
                            <input class="w-full bg-white border border-outline-variant rounded p-2 text-body-md font-body-md form-focus-ring text-center" type="text" value="2022">
                        </div>
                    </div>
                    <div>
                        <label class="font-label-caps text-[10px] text-on-surface-variant block mb-1">DOI</label>
                        <input class="w-full bg-white border border-outline-variant rounded p-2 text-data-mono font-data-mono form-focus-ring text-[12px]" type="text" value="10.1038/s41593-022-01050-x">
                    </div>
                </div>
        </div>
        
    

    </div>

    
</div>
<!-- /template -->