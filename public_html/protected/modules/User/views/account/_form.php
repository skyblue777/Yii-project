<?php
$script = "
$('#change_password').change(function(){
    if (\$(this).attr('checked')) {
        $('#User_password').parents('.row').show();
        $('#User_confirmPassword').parents('.row').show();
    } else {
        $('#User_password').val('').parents('.row').hide();
        $('#User_confirmPassword').val('').parents('.row').hide();
    }
});
";
Yii::app()->clientScript->registerScript(__CLASS__.'#ChangePassword', $script, CClientScript::POS_READY);
if ($model->isNewRecord == false)
    Yii::app()->clientScript->registerScript(__CLASS__.'#HideChangePassword', "\$('#User_password').parents('.row').hide();\$('#User_confirmPassword').parents('.row').hide();", CClientScript::POS_READY);
if (Yii::app()->user->checkAccess(FAuthManager::ROLE_ADMINISTRATORS) && $model->id == Yii::app()->user->id)
    Yii::app()->clientScript->registerScript(__CLASS__.'#DisableStatus', "\$('#User_status').parents('.row').hide();", CClientScript::POS_READY);
?>
<div class="form wide">
<?php $form=$this->beginWidget('FActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Fields with')?> <span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','are required')?></p>

	<?php echo $form->errorSummary($model); ?>

    <?php if ($model->IsNewRecord && $this->action->id=='createAdmin') echo Chtml::hiddenField('create_admin',1); ?>
    
    <?php if (! $model->IsNewRecord) echo $form->hiddenField($model, "id") ; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

    <?php if ($model->isNewRecord == false):?>
    <div class="row">
        <?php echo CHtml::label(Language::t(Yii::app()->language,'Backend.User.Form','Change Password'),'change_password'); ?>
        <?php echo CHtml::checkBox('change_password'); ?>
    </div>
    <?php endif;?>

    <div class="row">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password',array('size'=>32,'maxlength'=>32)); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

	<div class="row">
		<label for="User_confirmPassword"><?php echo Language::t(Yii::app()->language,'Backend.User.Form','Confirm new password')?></label>
		<?php echo $form->passwordField($model,'confirmPassword',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'confirmPassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

    <?php if (Yii::app()->user->checkAccess(FAuthManager::ROLE_ADMINISTRATORS)): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'status', array('label'=>'Active')); ?>
		<?php echo $form->checkbox($model,'status'); ?>
	</div>
    <?php endif; ?>

	<div>
		<?php echo CHtml::submitButton($model->isNewRecord ? Language::t(Yii::app()->language,'Backend.Common.Common','Create') : Language::t(Yii::app()->language,'Backend.Common.Common','Save'),array('class'=>'buttons')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->