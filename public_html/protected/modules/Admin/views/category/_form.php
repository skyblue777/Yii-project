<div class="form wide">

<?php $form=$this->beginWidget('FActiveForm', array(
	'id'=>'category-form',
	'enableAjaxValidation'=>false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); ?>
	<p class="note"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Fields with')?> <span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','are required')?>.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if (! $model->IsNewRecord) echo $form->hiddenField($model, "id") ; ?>
    <?php //$form->hiddenField($model, "ordering"); ?>
    <?php //$form->hiddenField($model, "parent_id"); ?>
    
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

    <div class="row" id="section_warning_page">
        <?php echo $form->labelEx($model,'warning_page'); ?>
        <?php echo $form->dropDownList($model,'warning_page',array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No')),array('prompt'=>'-- '.Language::t(Yii::app()->language,'Backend.Common.Common','Please Select').' --')); ?>
        <?php echo $form->error($model,'warning_page'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'parent_id'); ?>
        <?php $this->widget('CategoryDropDownList', array(
             'model'=>$model,
             'attribute'=>'parent_id',
             'root_id'=>AdsSettings::ADS_ROOT_CATEGORY,
             'htmlOptions'=>array(
                'multiple'=>true,
                'size'=>'6',
                 'prompt'=>'-- '.Language::t(Yii::app()->language,'Backend.Common.Common','Root').' --',
             ),
             'exclude'=>array($model->id),
//            'enableCache'=>true,
         )); ?>
        <?php echo $form->error($model,'parent_id'); ?>
    </div>
    
    <div class="row" id="section_show_ad_counter">
        <?php echo $form->labelEx($model,'show_ad_counter'); ?>
        <?php echo $form->dropDownList($model,'show_ad_counter',array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No')),array('prompt'=>'-- '.Language::t(Yii::app()->language,'Backend.Common.Common','Please Select').' --')); ?>
        <?php echo $form->error($model,'show_ad_counter'); ?>
    </div>    
    
    <div class="row" id="section_price_required">
        <?php echo $form->labelEx($model,'price_required'); ?>
        <?php echo $form->dropDownList($model,'price_required',array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No')),array('prompt'=>'-- '.Language::t(Yii::app()->language,'Backend.Common.Common','Please Select').' --')); ?>
        <?php echo $form->error($model,'price_required'); ?>
    </div>    

    <?php
    if ($model->parent_id==1){ 
        $this->widget('CategoryIconUploader',array('value'=>$model->image));
    }
    ?>

    <?php /*if ($model->parent_id==1){ ?>
    <div class="row" id="homepage_icon">
        <?php echo $form->labelEx($model,'homepage_icon'); ?>       
        <?php if ($model->image==''){ ?>            
        <?php //echo $form->fileField($model,'image'); ?> 
        <?php echo CHtml::fileField('iconImage',''); ?>
        <?php } else { 
        echo '<img src="'.Yii::app()->request->getBaseUrl(TRUE).'/uploads/category/'.$model->image.'" >';
        echo CHtml::ajaxButton(
        "Remove icon",
        array('category/deleteIcon','id'=>$model->id),
        array('success'=>
                'function(data){ jQuery("#actionresponse").html(data) }')
        );              

         } ?>
        <?php echo $form->error($model,'homepage_icon'); ?>
    </div>
    <?php } */?>

	<div class="row">
		<?php //echo CHtml::submitButton($model->isNewRecord ? Language::t(Yii::app()->language,'Backend.Common.Common','Create') : Language::t(Yii::app()->language,'Backend.Common.Common','Save'), array('class'=>'buttons'));
            echo CHtml::htmlButton($model->isNewRecord ? Language::t(Yii::app()->language,'Backend.Common.Common','Create') : Language::t(Yii::app()->language,'Backend.Common.Common','Save'), array('class'=>'buttons', 'onclick'=>'javascript: send();'));
        ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function(){    
    $('#menu ul li ul li').each(function(){
        if ($(this).find('a span.sub-menu-text strong').html()=='')
        {
            $(this).remove();    
        }
    });
});

function toggleIcon(img){
    if(img!=''){
        $('#Category_image').attr("src","<?php Yii::app()->request->getBaseUrl(TRUE).'/uploads/category/';?>"+img);
        $('#Category_image').show();
    }else{
        $('#Category_image').hide();
    }
}

$('#Category_parent_id > option:eq(0)').val('<?php echo AdsSettings::ADS_ROOT_CATEGORY; ?>');

$('#Category_parent_id').change(function(){
    var parent_id = $(this).val();
    if (parent_id == '<?php echo AdsSettings::ADS_ROOT_CATEGORY; ?>')
    {
        $('#section_price_required').hide();
        $('#Category_price_required').val('0');
        $('#section_show_ad_counter').show();    
    }
    else
    {
        $('#section_price_required').show();
        $('#section_show_ad_counter').hide();
        $('#Category_show_ad_counter').val('0');    
    }
});
$('#Category_parent_id').trigger('change');
</script> 

<script type="text/javascript">
function send(){
    var formData=$("#category-form").serialize();
    // $.ajax({
    //     url: '<?php //echo Yii::app()->request->getBaseUrl(TRUE)."/protected/modules/Admin/controllers/CategoryController.php/actionCreate"; ?>',
    //     type: 'POST',
    //     data: formData,
    //     datatype:'json'
    // });
 console.log(formData);
    //return false;
}
</script>