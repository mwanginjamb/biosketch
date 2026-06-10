<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\library\FormUi;

$this->title = 'Create a Bio Sketch Account';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Create a new account to start building with Yii2.';
$this->params['meta_keywords'] = 'yii, yii2, signup, register, create account';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>

<div class="mb-lg text-center">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs"><?= Html::encode($this->title) ?></h2>
    <p class="font-body-md text-body-md text-on-surface-variant">Start building your verified professional biosketch
        today.</p>
</div>

<!-- Form panel -->


<?php $form = ActiveForm::begin(FormUi::formConfig('signup-form')); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'staffID', FormUi::fieldConfig('id_card'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Staff Employee No']) ?>
<?= $form->field($model, 'username', FormUi::fieldConfig('account_circle'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Institutional Username']) ?>


<?= $form->field($model, 'email', FormUi::fieldConfig('mail'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Institutional E-mail Address', 'type' => 'email']) ?>



<?= $form->field($model, 'password', FormUi::fieldConfig('lock'))->passwordInput([
    'class' => FormUi::inputClass(true),
    'placeholder' => '••••••••',
    'autocomplete' => 'current-password'
])
    ?>

<?= $form->field($model, 'passwordConfirm', FormUi::fieldConfig('lock'))->passwordInput([
    'class' => FormUi::inputClass(true),
    'placeholder' => '••••••••',
    'autocomplete' => 'current-password'
])
    ?>




<?= Html::submitButton('Sign Up', ['class' => FormUi::buttonClass('auth')]) ?>

<?php ActiveForm::end(); ?>

<div class="text-primary text-center mt-3 small">
    Already have an account? <?= FormUi::link('Back to Login', ['site/login']) ?>
</div>