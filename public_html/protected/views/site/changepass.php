<?php
$this->pageTitle=Yii::app()->name . ' - Change Password';
$this->breadcrumbs=array(
    'Change Password',
);
?>
<h1>Change Password</h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'changepass-form',
	'enableAjaxValidation'=>false,
)); ?>

    <?php $this->widget('MessageBox'); ?>

	<div class="row">
		<?php echo CHtml::label('Username', ''); ?>
		<?php echo Yii::app()->user->username; ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'passwordOld'); ?>
        <?php echo $form->passwordField($model,'passwordOld',array('size'=>60,'maxlength'=>64)); ?>
        <?php echo $form->error($model,'passwordOld'); ?>
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
        <?php echo CHtml::htmlButton('Save', array('type'=>'submit')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
