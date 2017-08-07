<div class="form">

<?php echo FHtml::beginForm('', 'post', array('id'=>'comment-form')); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo FHtml::activeHiddenField($model, 'post_id'); ?>
	<?php echo FHtml::activeHiddenField($model, 'id'); ?>
	<div class="row">
		<?php echo FHtml::activeLabelEx($model,'author'); ?>
		<?php echo FHtml::activeTextField($model,'author',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo FHtml::error($model,'author'); ?>
	</div>

	<div class="row">
		<?php echo FHtml::activeLabelEx($model,'email'); ?>
		<?php echo FHtml::activeTextField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo FHtml::error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo FHtml::activeLabelEx($model,'url'); ?>
		<?php echo FHtml::activeTextField($model,'url',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo FHtml::error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo FHtml::activeLabelEx($model,'content'); ?>
		<?php echo FHtml::activeTextArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo FHtml::error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo FHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save'); ?>
	</div>

<?php echo FHtml::endForm(); ?>

</div><!-- form -->