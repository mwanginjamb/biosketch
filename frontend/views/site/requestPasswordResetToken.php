<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\PasswordResetRequestForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\library\FormUi;

$this->title = 'Reset your password';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Request a password reset link to recover access to your account.';
$this->params['meta_keywords'] = 'yii, yii2, password reset, forgot password, recovery';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>


            
<div class="mb-lg text-center">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs"><?= Html::encode($this->title) ?></h2>
    <p class="font-body-md text-body-md text-on-surface-variant">Make Password Reset Request.</p>
</div>
         
                    

                    <?php $form = ActiveForm::begin(FormUi::formConfig('signup-form')); ?>

                    <?= $form->errorSummary($model) ?>

                    
                    <?= $form->field($model, 'email',FormUi::fieldConfig('mail') )->textInput(['class' => FormUi::inputClass(true)])->label('Your Email', $labelOptions) ?>
                    

                    <?= Html::submitButton('Request Reset', ['class' => FormUi::buttonClass('auth')]) ?>

                        
                    

                    <?php ActiveForm::end(); ?>

                    <div class="text-body-secondary text-center mt-3 small">
                        Remember your password? <?= Html::a('Login', ['site/login']) ?>
                    </div>

                