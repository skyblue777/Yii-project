<div class="form wide">

<?php $form=$this->beginWidget('FActiveForm', array(
	'id'=>'cache-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if (! $model->IsNewRecord) echo $form->hiddenField($model, "id") ; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo CHtml::encode($model->name); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'expired', array('label'=>'Last Rebuild Cache')); ?>
        <?php echo Yii::app()->getDateFormatter()->formatDateTime(strtotime($model->expired)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'duration', array('label'=>'Cache time(second)')); ?>
        <?php echo $form->textField($model,'duration', array('cols'=>70, 'rows'=>7)); ?>
        <?php echo $form->error($model,'duration'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textarea($model,'description', array('cols'=>70, 'rows'=>7)); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Language::t(Yii::app()->language,'Backend.Common.Common','Create') : Language::t(Yii::app()->language,'Backend.Common.Common','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->