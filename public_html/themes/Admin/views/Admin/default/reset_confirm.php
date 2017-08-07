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
<h3>Please check your email?</h3>
                <style type="text/css">
                .row div.errorMessage {margin-left: 205px;}
                .form .row label  {width: 200px;}
                </style>
                <?php if (empty($email) == false):?>
                <p>An email has been sent to <strong><?php echo CHtml::encode($email);?></strong>.
                 This email describes how to get your new password.</p>
                <?php endif;?>
                <div class="form">
                <?php $form=$this->beginWidget('FActiveForm', array(
                    'id'=>'user-form',
                    'enableAjaxValidation'=>true,
                )); ?>
                <?php $this->widget('MessageBox'); ?>
                <p class="note">Please be patient; the delivery of email may be delayed. Remember to 
                confirm that the email above is correct and to check your junk or spam folder or filter if you do not receive this email.</p>
                    <div class="row">
                        <?php echo $form->labelEx($model,'validation_code'); ?>
                        <?php echo $form->textField($model,'validation_code',array('size'=>20,'maxlength'=>16)); ?>
                        <?php echo $form->error($model,'validation_code'); ?>
                        <div style="clear: both;">Please enter the confirmation code that was sent to you.
                         This is not the same as your password.</div>
                    </div>
                    <div class="row buttons">
                        <?php echo CHtml::button('Back',array(
                            'onclick'=>'document.location.href="'.Yii::app()->request->urlReferrer.'"',
                            'style'=>'width: 50px;',
                            ));?>
                        <?php echo CHtml::submitButton('Submit'); ?>Or
                        <?php echo CHtml::link('Login',array('/Admin/default/login'));?>
                    </div>
    

<?php $this->endWidget(); ?>

</div><!-- form -->
