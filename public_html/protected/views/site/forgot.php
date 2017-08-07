<?php
$this->pageTitle=Yii::app()->name . ' - Reset Password';
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Reset password')
);
?>

<h1><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Trouble Accessing Your Account?')?></h1>

<div class="form">
<?php $form=$this->beginWidget('FActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Message')?><br/><?php echo CHtml::link(Language::t(Yii::app()->language,'Frontend.GenericContent.ResetPassword','Have a confirmation code already?'), array('/site/resetConfirm'));?></p>

    <?php $this->widget('MessageBox'); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Common.Common','Continue')); ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->