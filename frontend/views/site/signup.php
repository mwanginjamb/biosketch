<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use common\library\FormUi;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
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

<div class="mb-lg text-center">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs"><?= Html::encode($this->title) ?></h2>
    <p class="font-body-md text-body-md text-on-surface-variant">Start building your verified professional biosketch
        today.</p>
</div>

<!-- Form panel -->


<?php $form = ActiveForm::begin(FormUi::formConfig('signup-form')); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'staffID', FormUi::fieldConfig('id_card'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Staff Employee No']) ?>

<!-- helper text -->
<p class="text-xs text-gray-500 mt-1">
    Enter your official employee number to auto-fill your account details.
</p>

<!-- loading state -->
<div id="employee-lookup-status" class="hidden text-xs mt-2 text-primary font-medium">
</div>

<!-- success state -->
<div id="employee-success" class="hidden text-sm mt-2 text-green-600 font-medium">
</div>

<!-- error state -->
<div id="employee-error" class="hidden text-sm mt-2 text-red-500 font-medium">
</div>

<?= $form->field($model, 'username', FormUi::fieldConfig('account_circle'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Institutional Username'])->label('Scientist Username') ?>


<?= $form->field($model, 'email', FormUi::fieldConfig('mail'))->textInput(['class' => FormUi::inputClass(true), 'placeholder' => 'Institutional E-mail Address', 'type' => 'email'])->label('E-mail Address') ?>



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


<?php

$lookupUrl = yii\helpers\Url::to(['site/lookup-employee']);

$js = <<<JS
const employeeInput =
    document.getElementById('signupform-staffid');

const usernameInput =
    document.getElementById('signupform-username');

const emailInput =
    document.getElementById('signupform-email');

const passwordInput =
    document.getElementById('signupform-password');

const submitBtn =
    document.getElementById('signup-submit-btn');

const statusBox =
    document.getElementById('employee-lookup-status');

const successBox =
    document.getElementById('employee-success');

const errorBox =
    document.getElementById('employee-error');

let debounceTimeout = null;

 
if (employeeInput) {

    employeeInput.addEventListener('input', function () {

        clearTimeout(debounceTimeout);

        const employeeNumber = this.value.trim();

        // reset states
        successBox.classList.add('hidden');
        errorBox.classList.add('hidden');

        if (!employeeNumber) {
            submitBtn.disabled = true;
            return;
        }

        debounceTimeout = setTimeout(async () => {

            try {

                statusBox.classList.remove('hidden');
                statusBox.innerText = 'Looking up employee...';

                const response = await fetch(
                    '{$lookupUrl}?employeeNumber=' +
                    encodeURIComponent(employeeNumber)
                );

                const data = await response.json();

                statusBox.classList.add('hidden');

                if (data.success) {

                    usernameInput.value =
                        data.username.toLowerCase();

                    emailInput.value =
                        data.email.toLowerCase();

                    successBox.innerText =
                        '✓ Employee verified successfully';

                    successBox.classList.remove('hidden');

                    errorBox.classList.add('hidden');

                    submitBtn.disabled = false;

                    passwordInput.focus();

                } else {

                    submitBtn.disabled = true;

                    errorBox.innerText =
                        data.message || 'Employee not found';

                    errorBox.classList.remove('hidden');

                    successBox.classList.add('hidden');

                }

            } catch (error) {

                console.error(error);

                submitBtn.disabled = true;

                statusBox.classList.add('hidden');

                errorBox.innerText =
                    'Could not connect to employee directory';

                errorBox.classList.remove('hidden');
            }

        }, 600);

    });

    /*
|--------------------------------------------------------------------------
| PASSWORD VISIBILITY TOGGLE
|--------------------------------------------------------------------------
*/

const togglePasswordBtn =
    document.getElementById('toggle-password');

togglePasswordBtn.addEventListener('click', function () {

    if (passwordInput.type === 'password') {

        passwordInput.type = 'text';

        this.innerText = 'Hide Password';

    } else {

        passwordInput.type = 'password';

        this.innerText = 'Show Password';
    }   
});
}

/*
|--------------------------------------------------------------------------
| PASSWORD STRENGTH METER
|--------------------------------------------------------------------------
*/

const strengthBar =
document.getElementById('password-strength-bar');

const strengthText =
document.getElementById('password-strength-text');

passwordInput.addEventListener('input', function () {

    const password = this.value;

    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    let width = '0%';
    let text = 'Weak';

    if (strength === 1) {
        width = '25%';
        text = 'Weak';
    }

    if (strength === 2) {
        width = '50%';
        text = 'Fair';
    }

    if (strength === 3) {
        width = '75%';
        text = 'Good';
    }

    if (strength === 4) {
        width = '100%';
        text = 'Strong';
    }

    strengthBar.style.width = width;

    strengthText.innerText =
        password.length ? 'Password strength: ' + text : 'Password strength';
});

JS;

$this->registerJs($js, View::POS_READY);
?>