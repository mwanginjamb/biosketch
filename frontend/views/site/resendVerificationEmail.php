<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ResendVerificationEmailForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\library\FormUi;

$this->title = 'Resend verification email';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Resend the verification email to confirm your account.';
$this->params['meta_keywords'] = 'yii, yii2, verification, email, resend, confirm account';

?>


<!-- Heading (logo + app name are in the layout's top band) -->
<div class="mb-md border-b border-outline-variant pb-sm text-center">
<h1 class="font-headline-lg text-headline-lg text-on-surface">Resend Account Activation Email</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-base">Enter your Signup E-mail Address to Receive the Activation Link</p>
</div>
   
            
           

                    <?php $form = ActiveForm::begin(FormUi::formConfig('resend-verification-email-form')); ?>

                    
                        <?= $form->field($model, 'email', FormUi::fieldConfig('mail'))->textInput(['class' => FormUi::inputClass(true),'placeholder' => 'user@mail.com',
            'type'=> 'email']) ?>
                   
                  
                        
                       
                    <?= Html::submitButton('Request', ['class' => FormUi::buttonClass('auth')]) ?>

                    <?php ActiveForm::end(); ?>

                   
                    
                    
                    <div class="mt-lg pt-lg border-t border-outline-variant flex flex-col items-center gap-md">
                        <?= FormUi::divider() ?>
                        <?= FormUi::link('Already verified?  ', ['site/login']) ?>
                            
                        </div>
                        
                    

               

 



