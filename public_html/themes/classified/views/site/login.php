<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Backend.Admin.Login','Sign in')
);
$this->pageTitle = Settings::SITE_NAME.' - Sign in';
?>

<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Backend.Admin.Login','Sign in'); ?></h1>

<div class="form login create-ads">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'form-login',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
    )); ?>

        <?php if (count($errorMsgs) > 0) : ?>
        <div class="errorMessage">
        <?php foreach($errorMsgs as $msg) : ?>
            <p><?php echo $msg; ?></p>
        <?php endforeach; ?>
        </div>                            
        <?php endif; ?>
        
        <div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email',array('style'=>'width: 150px;')); ?>
            <?php echo $form->error($model,'email'); ?>
        </div>
        
        <div class="row">
            <?php echo $form->labelEx($model,'password'); ?>
            <?php echo $form->passwordField($model,'password',array('style'=>'width: 150px;')); ?>
            <?php echo $form->error($model,'password'); ?>
        </div>
        
        <div style="margin-left: 158px;">
            <a href="<?php echo Yii::app()->createUrl('/site/forgotPassword')?>"><?php echo Language::t(Yii::app()->language,'Backend.Admin.Login','Forgot password')?></a>
            <div style="margin-top: 10px;"><input type="checkbox" name="remember" /><span><?php echo CHtml::label(Language::t(Yii::app()->language,'Backend.Admin.Login','Remember me on this computer'), 'remember'); ?></span></div>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Admin.Login','Sign in'),array('name'=>'btnLogin','class'=>'btn')); ?>
        </div>

    <?php $this->endWidget(); ?>
</div>