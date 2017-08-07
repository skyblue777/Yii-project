<h1>eBay Ads Parser</h1>
<div class="form wide">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'crawler-project-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php //echo $form->errorSummary($model); ?>
    
    <div class="row">
        <?php echo $form->labelEx($model,'contentUrl')?>
        <?php echo $form->textField($model,'contentUrl',array('size'=>50));?>
        <?php echo $form->error($model,'contentUrl'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'sourceLang')?>
        <?php echo $form->textField($model,'sourceLang',array('size'=>2,'value'=>'fr'));?>
        <?php echo $form->error($model,'sourceLang'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'targetLang')?>
        <?php echo $form->textField($model,'targetLang',array('size'=>2,'value'=>'en'));?>
        <?php echo $form->error($model,'targetLang'); ?>
    </div>
    <div class="row">
        <span class="error">* </span><span class="hint">For more details about the accepted language (API language code), </span><?php echo CHtml::link('Click here','http://onlinehelp.microsoft.com/en-us/bing/ff808526.aspx',array('target'=>'blank'));?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'appId')?>
        <?php echo $form->textField($model,'appId',array('size'=>50));?>
        <?php echo $form->error($model,'appId'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'backupAppId')?>
        <?php echo $form->textField($model,'backupAppId',array('size'=>50));?>
        <?php echo $form->error($model,'backupAppId'); ?>
    </div>
    <div class="row">
        <span class="error">* </span><span class="hint">Warning: This AppId is used during the parsing process in case the first AppID reaches limitation.</span>
    </div>
    <div class="row">
    <?php echo CHtml::link('Click here','http://www.bing.com/developers/appids.aspx',array('target'=>'blank'));?><span class="hint"> to create a new appID. </span>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'postEmail')?>
        <?php echo $form->textField($model,'postEmail',array('size'=>30,'value'=>'oliversulli@yahoo.com'));?>
        <?php echo $form->error($model,'postEmail'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'categoryId')?>
        <?php $this->widget('CategoryDropDownList', array(
            'model'=>$model,
            'attribute'=>'categoryId',
            //'name'=>'category_id',
            'htmlOptions'=>array(
                'prompt'=>'-- Please select --',
            ),
        )); ?>
        <?php echo $form->error($model,'categoryId'); ?>
    </div>
    <div class="row button">
        <?php echo CHtml::submitButton('Run');?>
    </div>
    
<?php $this->endWidget(); ?>

</div><!-- end-form -->

