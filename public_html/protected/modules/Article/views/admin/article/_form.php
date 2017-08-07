<div class="form wide">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'article-form',
	'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit'=>true,
    ),
)); ?>

	<p class="note"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Fields with')?> <span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','are required')?></p>

	<?php //echo $form->errorSummary($model); ?>

    <?php if (! $model->IsNewRecord) echo $form->hiddenField($model, "id") ; ?>
    
    <?php echo $form->hiddenField($model,'author_id', array('value' => user()->Id)); ?>
    <?php Yii::import('Language.models.LanguageForm');?>
	<div class="row">
		<?php echo $form->labelEx($model,'lang'); ?>
		<?php echo CHtml::activeDropDownList($model, 'lang', LanguageForm::getList_languages_exist());?>
		<?php echo $form->error($model,'lang'); ?>
	</div>
    <div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
		<?php $this->widget('CategoryDropDownList', array(
            'model' => $model,
            'attribute' => 'category_id',
            'root_id' => Settings::STATIC_PAGE_ROOT_CATEGORY,
        ));?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alias'); ?>
        <?php $this->widget('AliasInput', array(
            'model' => $model,
            'attribute' => 'alias',
            'htmlOptions' => array('size'=>60,'maxlength'=>512)
        ))?>
		<?php echo $form->error($model,'alias'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'leading_text'); ?>
        <?php $this->widget('Core.components.tinymce.ETinyMce', array(
            'model'=>$model, 
            'attribute'=>'leading_text', 
            'editorTemplate'=>'custom',
            'width'=>'700px',
            'height'=>'200px',
            'useCompression'=>false,
            'useElFinder'=>false,
        )); ?>
		<?php echo $form->error($model,'leading_text',array(
            'clientValidation' => "
                var leading_text = tinyMCE.get('Article_leading_text').getContent();
                leading_text = leading_text.replace(/&nbsp;/gi,'');
                leading_text = leading_text.replace(/\s/gi,'');
                leading_text = leading_text.replace(/<p><\/p>/gi,'');
                leading_text = jQuery.trim(leading_text);
                if (leading_text=='')
                {
                    messages.push(\"".Language::t(Yii::app()->language,'Backend.Article.Common','Leading Text').' '.Language::t(Yii::app()->language,'Backend.Article.Admin','cannot be blank')."\");
                    return false;
                }
                return false;
            ",
        )); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
        <?php $this->widget('Core.components.tinymce.ETinyMce', array(
            'model'=>$model, 
            'attribute'=>'content', 
            'editorTemplate'=>'custom',
            'width'=>'700px',
            'height'=>'400px',
            'useCompression'=>false,
            'useElFinder'=>false,
        )); ?>
        <?php echo $form->error($model,'content'); ?>
	</div>
    <!--
	<div class="row">
		<?php echo $form->labelEx($model,'photo'); ?>
		<?php echo $form->textField($model,'photo',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'photo'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'model' => $model,
                'attribute'=>'tags',
                'source'=> 'js:function( request, response ) {
                    $.ajax({
                        url: "'.serviceUrl('Article.ArticleAPI.searchTags').'",
                        dataType: "json",
                        data: {term: request.term.split( /,\s*/ ).pop()},
                        complete: function( obj ) {
                            data = eval(obj.responseText);
                            response( $.map( data.matches, function( item ) {
                                return {
                                    label: item.name,
                                    value: item.name
                                }
                            }));
                        }
                    });
                }',
                'options' => array(
                    'focus' => 'js:function() {
                        // prevent value inserted on focus
                        return false;
                    }',
                    'select' => 'js:function( event, ui ) {
                        var terms = this.value.split( /,\s*/ );
                        // remove the current input
                        terms.pop();
                        // add the selected item
                        terms.push( ui.item.value );
                        // add placeholder to get the comma-and-space at the end
                        terms.push( "" );
                        this.value = terms.join( ", " );
                        return false;
                    }',
                ),
                'htmlOptions'=>array(
                    'size' => 60,
                )
        ));?>
		<?php echo $form->error($model,'tags'); ?>
	</div>
    -->

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status', Lookup::items('status')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

    <?php if (! $model->IsNewRecord) : ?>
    <div class="row">
        <?php echo $form->labelEx($model,'create_time'); ?>
        <?php echo Yii::app()->getDateFormatter()->formatDateTime($model->create_time); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'update_time'); ?>
        <?php echo Yii::app()->getDateFormatter()->formatDateTime($model->update_time); ?>
    </div>
    <?php endif;?>

	<div>
		<?php echo CHtml::submitButton($model->isNewRecord ? Language::t(Yii::app()->language,'Backend.Common.Common','Create') : Language::t(Yii::app()->language,'Backend.Common.Common','Save'),array('class'=>'buttons')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->