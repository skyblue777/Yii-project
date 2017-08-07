<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password')
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password');
?>

<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password'); ?></h1>

<div class="form forgot-password create-ads">
  <?php if ($resetSuccessfully): ?>
    <div style="margin-top: 0px;"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.After_reset_password','')?></div>
  <?php else : ?>  
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'form-forgot-password',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
    )); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
            <?php echo $form->error($model,'email'); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model,'verifyCode'); ?>
            <?php echo $form->textField($model,'verifyCode',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
            <?php echo $form->error($model,'verifyCode'); ?>
        </div>
        <div style="clear: both;"></div>
        <div style="margin-left: 158px; margin-top: 0px;">
            <?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'imageOptions'=>array('id'=>'img-captcha'))); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Common.Common','Send'),array('name'=>'btnForgotPassword','class'=>'btn')); ?>
        </div>

    <?php $this->endWidget(); ?>
  <?php endif; ?>
</div>