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
                'enableClientValidation'=>true,
                'clientOptions'=>array(
                    'validateOnSubmit'=>true,
                ))); 
        ?>
    <?php $this->widget('MessageBox'); ?>
    <div class="row">
                    Forgot your password? Enter your login email below and fill the security check.
                     We will send you an email with a link to reset your password.<br/>
    <?php echo CHtml::link('Have a confirmation code already?', url('/Admin/default/resetPassword'));?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::button('Back',array('onclick'=>'document.location.href="'.Yii::app()->request->urlReferrer.'"','style'=>'width: 50px;',));?>
        <?php echo CHtml::submitButton('Continue'); ?> Or
        <?php echo CHtml::link('Login',array('/Admin/default/login'));?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>
