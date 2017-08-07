<?php
$this->pageTitle=Yii::app()->name . ' - Reset Password';
$this->breadcrumbs=array(
    'Reset Password',
);
?>
<h1>Reset Password</h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

    <?php $this->widget('MessageBox'); ?>
    
    <div class="row">
        <?php echo CHtml::label('Email', ''); ?>
        <?php echo $model->email; ?>
    </div>

	<div class="row">
		<?php echo CHtml::label('Username', ''); ?>
		<?php echo $model->username; ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>64)); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'confirmPassword'); ?>
		<?php echo $form->passwordField($model,'confirmPassword',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'confirmPassword'); ?>
	</div>

	<div class="row buttons">
        <label>&nbsp;</label>
        <?php echo CHtml::htmlButton('Change Password', array('type'=>'submit')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
