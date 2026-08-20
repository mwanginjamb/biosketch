<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var string|null $content */

$this->title = 'Researcher BioSketch';

?>

<div class="row">

    <div class="col-md-12">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Researcher BioSketch
                </h3>

            </div>

            <div class="card-body">

                <?php if (empty($content)): ?>

                    <p class="alert alert-info">
                        Researcher BioSketch is not available.
                    </p>

                <?php else: ?>

                    <iframe src="data:application/pdf;base64,<?= $content ?>" height="950px" width="100%"
                        style="border: 0;"></iframe>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>