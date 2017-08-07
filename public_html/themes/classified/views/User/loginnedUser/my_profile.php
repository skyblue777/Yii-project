<?php
$this->breadcrumbs=array(
 	Language::t(Yii::app()->language,'Frontend.User.Login','Register')  
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.User.Login','My Profile');
?>

<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<div class="link"> 
<span class="active">
<a href="<?php echo $this->createUrl('/User/loginnedUser/viewMyAds'); ?>">
<?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My ads')?>
</a> 
</span> 
<span>|</span> 
<a href="<?php echo $this->createUrl('/User/loginnedUser/viewMyFavoriteAds'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My Favorites')?></a> 
<span>|</span> 
<a href="<?php echo $this->createUrl('/User/loginnedUser/myProfile'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My Profile')?></a>
</div>
<h1 class="title-3"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My profile')?></h1>

<div class="form from-edit-profile create-ads">
    <?php if ($updateSuccessfully): ?>
        <div style="margin-top: 0px;"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','Your profile has been updated successfully.')?></div>
    <?php else : ?>
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'form-edit-profile',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit'=>true,
            ),
        )); ?>
            
            <div class="row">
                <?php echo $form->labelEx($model,'email'); ?>
                <div class="group-1"><?php echo $model->email; ?></div>
            </div>
            
            <div class="row">
                <?php echo $form->labelEx($model,'first_name'); ?>
                <?php echo $form->textField($model,'first_name',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
                <?php echo $form->error($model,'first_name'); ?>
            </div>
            
            <div class="row">
                <?php echo $form->labelEx($model,'last_name'); ?>
                <?php echo $form->textField($model,'last_name',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
                <?php echo $form->error($model,'last_name'); ?>
            </div>
            
            <div style="margin-left: 158px;">
                <a class="lnk-show-section-change-pass" href="#"><?php echo Language::t(Yii::app()->language,'Backend.User.Form','Change password')?></a>
            </div>
            
            <div class="section-change-pass" style="display: none;">
                <div class="row">
                    <?php echo $form->labelEx($model,'password'); ?>
                    <?php echo $form->passwordField($model,'password',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
                    <?php echo $form->error($model,'password'); ?>
                </div>
                
                <div class="row">
                    <?php echo $form->labelEx($model,'confirmPassword'); ?>
                    <?php echo $form->passwordField($model,'confirmPassword',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
                    <?php echo $form->error($model,'confirmPassword'); ?>
                </div>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Common.Common','Update'),array('name'=>'btnUpdate','class'=>'btn')); ?>
            </div>

        <?php $this->endWidget(); ?>
        
        <script type="text/javascript">
        $('a.lnk-show-section-change-pass').click(function(){
            if ($('.section-change-pass').is(':visible'))
            {
                $('.section-change-pass').slideUp('fast');
                $('.section-change-pass input').val('');
            }
            else
            {
                $('.section-change-pass').slideDown('fast');    
            }
            return false;        
        });
        </script>    
    <?php endif; ?>
</div>