<div class="login">
<h1 style="font-size: 24px;">
    <a href="<?php echo baseUrl(); ?>" style="text-decoration: none; color: #0000ff;">
        <?php
        if (Settings::SITE_LOGO!='' && Settings::SITE_LOGO!='none')
            echo CHtml::image(baseUrl().'/'.'uploads/'.Settings::SITE_LOGO,'Logo',array('class'=>'site-logo', 'width' => 300, 'height' =>84));
        else
            echo Settings::SITE_NAME;
        ?>
    </a>
</h1>
<div class="form">

    <?php $form=$this->beginWidget('FActiveForm', array(
                        'id'=>'user-form',
                        'enableAjaxValidation'=>false,
                    )); ?>
    <?php $this->widget('MessageBox'); ?>
    <div class="row">
                    <p class="note">Please confirm that you would like to reset the password. We will then send you an email with a link to reset your password.</p>
                     <?php echo CHtml::link('Have a confirmation code already?', url('/Admin/default/resetPassword'));?>
                     </div>
                     <?php if(CCaptcha::checkRequirements()): ?>
                        <div class="row">
                            <label>&nbsp;</label>
                            <div><?php $this->widget('CCaptcha'); ?></div>
                        </div>
                        
                        <div class="row">
                            <?php echo $form->labelEx($model,'verifyCode'); ?>
                            <?php echo $form->textField($model,'verifyCode'); ?>
                            <div style="clear: both;">Please enter the letters as they are shown in the image above.
                            <br/>Letters are not case-sensitive.</div>
                        </div>
                        <?php endif; ?>

                        <div class="row buttons">
                            <?php echo CHtml::button('Back',array(
                                'onclick'=>'document.location.href="'.Yii::app()->createUrl('/Admin/default/forgotPassword').'"',
                                'style'=>'width: 50px;',
                                ));?>
                            <?php echo CHtml::submitButton('Continue'); ?>Or
                            <?php echo CHtml::link('Login',array('/Admin/default/login'));?>
                        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->
</div>
