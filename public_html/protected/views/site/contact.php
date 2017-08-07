<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','Contact')
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','Contact');
?>

<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','Contact'); ?></h1>

<div class="form contact create-ads">
  <?php if ($sendSuccessfully): ?>
    <div style="margin-top: 0px;">
      <p><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','Thank you for contacting us!'); ?></p>
      <p><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','We will get back to you as soon as possible.'); ?></p>
    </div>
  <?php else : ?>  
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'form-contact',
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
            <?php echo $form->labelEx($model,'subject'); ?>
            <?php echo $form->textField($model,'subject',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
            <?php echo $form->error($model,'subject'); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model,'message'); ?>
            <?php echo $form->textArea($model,'message',array('rows'=>10, 'cols'=>100)); ?>
            <?php echo $form->error($model,'message'); ?>
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
            <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Common.Common','Send'),array('name'=>'btnContact','class'=>'btn')); ?>
        </div>

    <?php $this->endWidget(); ?>
  <?php endif; ?>
</div>