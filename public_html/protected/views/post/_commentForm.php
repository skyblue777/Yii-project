<div class="form">

<?php $form=$this->beginWidget('FActiveForm', array(
	'id'=>'comment-form',
	'enableAjaxValidation'=>false,
    'action'=>$this->createUrl('Core/service/ajax'),
    'clientOptions'=>array(
        'afterValidate'=>"js:function(form, data, hasError){
            form.get(0).validateOnly.value=0;
            $.post(
                form.get(0).action,
                form.serialize(),
                function(json){
                    result = eval(json);
                    if (result.errors.length==0){
                        alert('Thank you, we have received your comment.');
                        $('#comment-form').slideUp();
                        $('body').trigger('CommentPosted',[result.model.content,'".$model->post->title."']);
                    }
                }
            );
            return false;
        }"
    ),
)); ?>
    <?php 
    //For ajax validation and save, as this form post directly to service
    echo CHtml::hiddenField('SID','Blog.comment.save'); 
    echo CHtml::hiddenField('validateOnly',1); 
    ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if (! $model->IsNewRecord) echo $form->hiddenField($model, "id") ; ?>
    <?php echo $form->hiddenField($model,'post_id'); ?>
    <?php echo $form->hiddenField($model,'status'); ?>
    
    <div class="row">
        <?php echo $form->labelEx($model,'author'); ?>
        <?php echo $form->textField($model,'author',array('size'=>60,'maxlength'=>64)); ?>
        <?php echo $form->error($model,'author'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'url'); ?>
        <?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'url'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->