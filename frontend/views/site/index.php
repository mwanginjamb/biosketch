<?php

use yii\bootstrap5\Html;

use frontend\assets\DtAsset;
DtAsset::register($this);

$this->title = 'List of Pulished BioSketches';
?>

<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-lg">
        <div>
            <h1 class="font-display text-display text-primary tracking-tight">Published Biosketches</h1>
            <p class="text-on-surface-variant font-body-lg">Manage and update your NIH and NSF formatted professional
                profiles.</p>
        </div>

        <?= Html::a(' <span class="material-symbols-outlined">add_circle</span> Create New', ['researcher/create'], ['title' => 'Create Your Bio Sketch', 'class' => 'flex items-center justify-center gap-xs bg-primary text-on-primary px-lg py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition-all']) ?>

    </div>
    <!-- Table Controls -->
    <div
        class="bg-surface-container-lowest border border-outline-variant p-md flex flex-col sm:flex-row items-center justify-between gap-md rounded-t-xl">

        <div class="flex items-center gap-xs">
            <button id="filter-all"
                class="px-md py-1.5 rounded-full bg-primary text-on-primary font-label-caps text-label-caps">
                All
            </button>

            <button id="filter-draft"
                class="px-md py-1.5 rounded-full hover:bg-surface-container-high text-on-surface-variant font-label-caps text-label-caps">
                Drafts
            </button>

            <button id="filter-published"
                class="px-md py-1.5 rounded-full hover:bg-surface-container-high text-on-surface-variant font-label-caps text-label-caps">
                Published
            </button>
        </div>

        <div class="relative w-full sm:w-64">

            <span
                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">
                search
            </span>

            <input id="biosketch-filter" type="text" placeholder="Filter biosketches..."
                class="w-full pl-9 pr-4 py-2 border border-outline-variant bg-surface-container-low rounded-lg text-body-md focus:border-secondary focus:ring-0">

        </div>

    </div>

    <!-- Data Table -->
    <div
        class="bg-surface-container-lowest border-x border-b border-outline-variant overflow-hidden rounded-b-xl shadow-sm">

        <div class="overflow-x-auto">

            <table id="researchers-table" class="w-full text-left border-collapse">

                <thead class="bg-surface-container-low border-b border-outline-variant">

                    <tr>
                        <th class="px-md py-4">Profile</th>
                        <th class="px-md py-4">Role</th>
                        <th class="px-md py-4">Department</th>
                        <th class="px-md py-4">Last Updated</th>
                        <th class="px-md py-4">Status</th>
                        <th class="px-md py-4 text-center">Actions</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-outline-variant">

                    <?php foreach ($models as $model): ?>

                        <tr class="hover:bg-surface-container-low/50 transition-colors">

                            <td class="px-md py-4">

                                <div class="flex items-center gap-sm">

                                    <div
                                        class="h-10 w-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">
                                        <?= Html::encode($model->title) ?>
                                    </div>

                                    <div>

                                        <div class="font-body-md font-bold text-primary">
                                            <?= Html::encode($model->full_name) ?>
                                        </div>

                                        <div class="text-xs text-on-surface-variant">
                                            <?= Html::encode($model->email) ?>
                                        </div>

                                    </div>

                                </div>

                            </td>

                            <td class="px-md py-4 text-on-surface-variant">
                                <?= Html::encode($model->role_title) ?>
                            </td>

                            <td class="px-md py-4 text-on-surface-variant">
                                <?= Html::encode($model->department) ?>
                            </td>

                            <td class="px-md py-4 font-data-mono text-on-surface-variant">
                                <?= Yii::$app->formatter->asDate($model->updated_at) ?>
                            </td>

                            <td class="px-md py-4">

                                <?php if ($model->status === 1): ?>

                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                        PUBLISHED
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                                        DRAFT
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="px-md py-4">

                                <div class="flex justify-center gap-xs">

                                    <?= Html::a(
                                        '<span class="material-symbols-outlined">visibility</span>',
                                        ['researcher/view', 'id' => $model->id],
                                        [
                                            'class' => 'p-2 hover:bg-primary-container hover:text-on-primary-container rounded transition-colors',
                                            'title' => 'View'
                                        ]
                                    ) ?>

                                    <?= Html::a(
                                        '<span class="material-symbols-outlined">edit</span>',
                                        ['researcher/update', 'id' => $model->id],
                                        [
                                            'class' => 'p-2 hover:bg-primary-container hover:text-on-primary-container rounded transition-colors',
                                            'title' => 'Edit'
                                        ]
                                    ) ?>

                                    <!-- report button -->
                                    <?= Html::a(
                                        '<span class="material-symbols-outlined">description</span>',
                                        ['researcher/report', 'id' => $model->id],
                                        [
                                            'class' => 'p-2 hover:bg-primary-container hover:text-on-primary-container rounded transition-colors',
                                            'title' => 'View Report'
                                        ]
                                    ) ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>












<?php

$js = <<<JS

const table = new DataTable('#researchers-table', {

    responsive: true,
    ordering: true,
    pageLength: 10,

    responsive: true,
    columnDefs: [
        {
            targets: 5, // Actions
            searchable: false,
            orderable: false
        }
    ],

    layout: {

        topStart: {
            buttons: [
               // 'copy',
               // 'csv',
               // 'excel',
               // 'pdf',
              //  'print'
            ]
        },

        topEnd: null

    }

});

$('#biosketch-filter').on('keyup', function () {
    table.search(this.value).draw();
});

// filtering using the chip buttons

function activateFilter(buttonId)
{
    $('#filter-all,#filter-draft,#filter-published')
        .removeClass('bg-primary text-on-primary')
        .addClass('hover:bg-surface-container-high text-on-surface-variant');

    $(buttonId)
        .removeClass('hover:bg-surface-container-high text-on-surface-variant')
        .addClass('bg-primary text-on-primary ');
}

$('#filter-all').click(function() {

    activateFilter('#filter-all');

    table.column(4).search('').draw();

});

$('#filter-draft').click(function() {

    activateFilter('#filter-draft');

    table.column(4).search('DRAFT').draw();

});

$('#filter-published').click(function() {

    activateFilter('#filter-published');

    table.column(4).search('PUBLISHED').draw();

});
JS;

$this->registerJs($js);

?>