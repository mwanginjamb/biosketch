<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\library\FormUi;

$this->title = 'Set your new password';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Set a new password for your account.';
$this->params['meta_keywords'] = 'yii, yii2, reset password, new password, security';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];



 /*$form->field($model, 'password', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128274;'),
                            'inputOptions' => [
                                'autofocus' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Password',
                            ],
                        ])->passwordInput()->label('New Password', $labelOptions); */

?>

<!-- Heading (logo + app name are in the layout's top band) -->
<div class="mb-md border-b border-outline-variant pb-sm text-center">
<h1 class="font-headline-lg text-headline-lg text-on-surface">Password Reset</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-base">Reset Your Password Below:</p>
</div>

           
              
                   

                    <?php $form = ActiveForm::begin(FormUi::formConfig('reset-password-form')); ?>

                   
                        <?= $form->field($model, 'password',FormUi::passwordFieldConfig() )->passwordInput([
                            'class' => FormUi::inputClass(true),
                            'placeholder' => '••••••••',
                            'autocomplete'=> 'current-password',
                            'id'          => 'passwordresetform-password'
                            ])->label('New Password', $labelOptions) ?>


 <?= $form->field($model, 'passwordConfirm',FormUi::passwordFieldConfig() )->passwordInput([
                            'class' => FormUi::inputClass(true),
                            'placeholder' => '••••••••',
                            'autocomplete'=> 'current-password',
                            'id'          => 'passwordresetform-password'
                            ])->label('New Password', $labelOptions) ?>
              

                    
                       <?= Html::submitButton('Reset Password', ['class' => FormUi::buttonClass('auth')]) ?>
                

                    <?php ActiveForm::end(); ?>

            
          


