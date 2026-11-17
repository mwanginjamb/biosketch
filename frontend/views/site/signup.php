<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use common\library\FormUi;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Create a Bio Sketch Account';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Create a new account to start building with Yii2.';
$this->params['meta_keywords'] = 'yii, yii2, signup, register, create account';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>

<?php $this->params['cardWidth'] = 'max-w-[640px]'; // two-column form on md+ ?>
<div class="mb-md text-center">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs"><?= Html::encode($this->title) ?></h2>
    <p class="font-body-md text-body-md text-on-surface-variant">Start building your verified professional biosketch
        today.</p>
</div>

<!-- Form panel -->


<?php $form = ActiveForm::begin(FormUi::formConfig('signup-form')); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'staffID', FormUi::fieldConfig('id_card'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Staff Employee No']) ?>

<!-- helper text -->
<p class="text-xs text-on-surface-variant mt-1">
    Enter your official employee number to auto-fill your account details.
</p>

<!-- loading state -->
<div id="employee-lookup-status" class="hidden text-xs mt-2 text-primary font-medium">
</div>

<!-- success state -->
<div id="employee-success" class="hidden text-sm mt-2 text-green-700 font-medium">
</div>

<!-- error state -->
<div id="employee-error" class="hidden text-sm mt-2 text-red-500 font-medium">
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<?= $form->field($model, 'username', FormUi::fieldConfig('account_circle'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Username'])->label('Scientist Username') ?>

<?= $form->field($model, 'email', FormUi::fieldConfig('mail'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Institutional e-mail', 'type' => 'email'])->label('E-mail Address') ?>

<?= $form->field($model, 'password', FormUi::fieldConfig('lock'))->passwordInput([
    'class' => FormUi::inputClass(true),
    'placeholder' => '••••••••',
    'autocomplete' => 'new-password'
])
    ?>

<?= $form->field($model, 'passwordConfirm', FormUi::fieldConfig('lock'))->passwordInput([
    'class' => FormUi::inputClass(true),
    'placeholder' => '••••••••',
    'autocomplete' => 'new-password'
])
    ?>
</div>




<?= Html::submitButton('Sign Up', [
    'id' => 'signup-submit-btn',
    'class' => FormUi::buttonClass('auth') . ' disabled:opacity-60 disabled:cursor-wait',
]) ?>

<?php ActiveForm::end(); ?>

<div class="text-primary text-center mt-3 small">
    Already have an account? <?= FormUi::link('Back to Login', ['site/login']) ?>
</div>


<?php

$lookupUrl = Json::htmlEncode(yii\helpers\Url::to(['site/lookup-employee']));

$js = <<<JS
(function () {
    const form = document.getElementById('signup-form');
    const submitBtn = document.getElementById('signup-submit-btn');
    const employeeInput = document.getElementById('signupform-staffid');
    const usernameInput = document.getElementById('signupform-username');
    const emailInput = document.getElementById('signupform-email');
    const passwordInput = document.getElementById('signupform-password');
    const statusBox = document.getElementById('employee-lookup-status');
    const successBox = document.getElementById('employee-success');
    const errorBox = document.getElementById('employee-error');

    const show = (el, text) => { if (el) { el.textContent = text; el.classList.remove('hidden'); } };
    const hide = (el) => { if (el) { el.classList.add('hidden'); } };

    // ── Staff ID lookup: prefill username and email ───────────────────
    let debounceTimeout = null;
    if (employeeInput) {
        employeeInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            const employeeNumber = this.value.trim();
            hide(successBox);
            hide(errorBox);
            if (!employeeNumber) {
                hide(statusBox);
                return;
            }

            debounceTimeout = setTimeout(async () => {
                show(statusBox, 'Looking up employee...');
                let data = null;
                try {
                    const response = await fetch({$lookupUrl} + '?employeeNumber=' + encodeURIComponent(employeeNumber), {
                        headers: { 'Accept': 'application/json' },
                    });
                    data = await response.json();
                } catch (error) {
                    console.error(error);
                }
                hide(statusBox);

                if (!data) {
                    show(errorBox, 'Could not connect to the employee directory. You can still fill in your details.');
                    return;
                }
                if (!data.success) {
                    show(errorBox, data.message || 'Employee not found');
                    return;
                }

                if (usernameInput) { usernameInput.value = (data.username || '').toLowerCase(); }
                if (emailInput) { emailInput.value = (data.email || '').toLowerCase(); }
                show(successBox, '✓ Employee verified');
                if (passwordInput) { passwordInput.focus(); }
            }, 600);
        });
    }

    // ── Submit feedback: sending the verification email can take a few seconds ──
    // 'beforeSubmit' fires only after client-side validation has passed.
    if (form && window.jQuery) {
        jQuery(form).on('beforeSubmit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="material-symbols-outlined animate-spin" aria-hidden="true">progress_activity</span>'
                    + ' Creating your account…';
            }
            return true;
        });
    }
})();
JS;

$this->registerJs($js, View::POS_READY);
?>
