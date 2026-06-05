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

<!-- Brand Identity -->
<div class="flex flex-col items-center mb-lg">
<div class="flex items-center gap-xs mb-sm">
<span class="material-symbols-outlined text-primary text-[32px]" data-icon="biotech">biotech</span>
<h1 class="font-headline-md text-headline-md font-bold text-primary tracking-tight">BioSketch Professional</h1>
</div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">Clinical Grade Precision</p>
</div>
<!-- Sign In Heading -->
<div class="mb-lg border-b border-outline-variant pb-sm text-center">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Sign In</h2>
<p class="font-body-md text-body-md text-on-surface-variant mt-base">Access your secure institutional research profile.</p>
</div>
   
            
           

                    <?php $form = ActiveForm::begin(FormUi::formConfig('login-form')); ?>

                    
                        <?= $form->field($model, 'username', FormUi::fieldConfig('account_circle'))->textInput(['class' => FormUi::inputClass(true),'placeholder' => 'e.g. m3mwabu',
            'autocomplete'=> 'username']) ?>
                   
                  
                        
                        <?= $form->field($model, 'password', FormUi::passwordFieldConfig())->passwordInput([
                            'class' => FormUi::inputClass(true),
                            'placeholder' => '••••••••',
                            'autocomplete'=> 'current-password',
                            'id'          => 'loginform-password'
                            ]) ?>
                    
                        <?= $form
                            ->field($model, 'rememberMe', FormUi::checkboxFieldConfig())
                            ->checkbox(FormUi::checkboxConfig('Keep me signed in')) ?>

                    <?= Html::submitButton('Sign In', ['class' => FormUi::buttonClass('auth')]) ?>

                    <?php ActiveForm::end(); ?>

                   
                    
                    <?= FormUi::link('Resend Verification Email? ', ['site/resend-verification-email']) ?>

                        <div class="mt-lg pt-lg border-t border-outline-variant flex flex-col items-center gap-md">
                            <?= FormUi::divider() ?>
                            <?= FormUi::secondaryButton('Create an account', '', ['site/signup']) ?>
                        </div>
                        
                    

               

 

