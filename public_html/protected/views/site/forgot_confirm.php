<?php
$this->pageTitle=Yii::app()->name . ' - '.Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password');
$this->breadcrumbs=array(
     Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password')
);
?>

<h1><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Confirm password reset request?')?></h1>
<div class="form">
<?php $form=$this->beginWidget('FActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>false,
)); ?>

    <?php $this->widget('MessageBox'); ?>

    <p class="note"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','ConfirmMessage')?></p>
    
    <?php if(CCaptcha::checkRequirements()): ?>
    <div class="row">
        <label>&nbsp;</label>
        <div><?php $this->widget('CCaptcha'); ?></div>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'verifyCode'); ?>
        <?php echo $form->textField($model,'verifyCode'); ?>
        <div style="clear: both;"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','ConfirmCodeMessage')?></div>
    </div>
    <?php endif; ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Common.Common','Continue')); ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->