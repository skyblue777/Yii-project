<?php
$this->pageTitle=Yii::app()->name . ' - Reset Password';
$this->breadcrumbs=array(
    'Reset Password'=>array('/site/forgot'),
    'Confirmation Code',
);
?>

<h1>Please check your email?</h1>
<style type="text/css">
.row div.errorMessage {margin-left: 205px;}
.form .row label  {width: 200px;}
</style>

<?php if (empty($email) == false):?>
<p>An email has been sent to <strong><?php echo CHtml::encode($email);?></strong>. This email describes how to get your new password.</p>
<?php endif;?>

<div class="form">
<?php $form=$this->beginWidget('FActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>true,
)); ?>

    <?php $this->widget('MessageBox'); ?>

    <p class="note">Please be patient; the delivery of email may be delayed. Remember to confirm that the email above is correct and to check your junk or spam folder or filter if you do not receive this email.</p>
    
    <div class="row">
        <?php echo $form->labelEx($model,'validation_code'); ?>
        <?php echo $form->textField($model,'validation_code',array('size'=>20,'maxlength'=>16)); ?>
        <?php echo $form->error($model,'validation_code'); ?>
        <div style="clear: both;">Please enter the confirmation code that was sent to you. This is not the same as your password.</div>
    </div>

    <div class="row buttons">
        <label>&nbsp;</label>
        <?php echo CHtml::htmlButton('Submit', array('type'=>'submit')); ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->