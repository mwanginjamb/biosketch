<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\library\FormUi;

$this->title = 'Login to your account';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Log in to access your Yii2 application account.';
$this->params['meta_keywords'] = 'yii, yii2, login, sign in, authentication';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>

<!-- Sign In Heading (logo + app name are in the layout's top band) -->
<div class="mb-md border-b border-outline-variant pb-sm text-center">
    <h1 class="font-headline-lg text-headline-lg text-on-surface">Sign In</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-base">Access your secure institutional research
        profile.</p>
</div>




<?php $form = ActiveForm::begin(FormUi::formConfig('login-form')); ?>


<?= $form->field($model, 'username', FormUi::fieldConfig('account_circle'))->textInput(['class' => FormUi::inputClassBare(), 'placeholder' => 'e.g. m3mwabu', 'autocomplete' => 'username']) ?>



<?= $form->field($model, 'password', FormUi::passwordFieldConfig())->passwordInput([
    'class' => FormUi::inputClassBare(),
    'placeholder' => '••••••••',
    'autocomplete' => 'current-password',
    'id' => 'loginform-password'
]) ?>

<?= $form
    ->field($model, 'rememberMe', FormUi::checkboxFieldConfig())
    ->checkbox(FormUi::checkboxConfig('Keep me signed in')) ?>

<?= Html::submitButton('Sign In', ['class' => FormUi::buttonClass('auth')]) ?>

<?php ActiveForm::end(); ?>



<div class="mt-xs">
    <?= FormUi::link('Resend Verification Email? ', ['site/resend-verification-email']) ?>
</div>

<div class="mt-sm flex flex-col items-center gap-sm">
    <?= FormUi::divider() ?>
    <?= FormUi::secondaryButton('Create an account', '', ['site/signup']) ?>
    <a href="<?= \yii\helpers\Url::toRoute(['site/tutorial']) ?>"
        class="inline-flex items-center gap-xs text-secondary hover:text-primary hover:underline font-label-md text-label-md transition-colors">
        <span class="material-symbols-outlined text-xl" aria-hidden="true">play_circle</span>
        New here? Watch the 3-minute tutorial
    </a>
</div>