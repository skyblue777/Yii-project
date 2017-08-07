<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Email this ad to a friend')?></h1>

<div class="form email-ad-to-friend create-ads">
    <?php if (is_null($ad)) : ?>
        <div class="errorMessage" style="margin-left: 0px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Sorry! This ad does not exist.')?></div>
    <?php elseif ($sendSuccessfully): ?>
        <div style="margin-top: 0px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Your message has been sent!')?></div>
        <?php
        $urlParams = array('id'=>$ad->id,
                           'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
        if ($ad->area != '')
          $urlParams['area'] = $ad->area;
        ?>
        <a style="margin: 10px 0;" href="<?php echo $this->createUrl('/Ads/ad/viewDetails',$urlParams); ?>" class="btn">
          <?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Back to ad')?>
        </a>
    <?php else : ?>
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'reply-to-ad-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit'=>true,
            ),
        )); ?>
            
            <div class="row">
                <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Ad')?>:</label>
                <div class="group-1"><?php echo $ad->title; ?></div>
            </div>
            
            <div class="row">
                <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','From (Your email)')?>:</label>
                <?php if (!Yii::app()->request->isPostRequest && !Yii::app()->user->isGuest) $model->senderEmail = Yii::app()->user->email; ?>
                <?php echo $form->textField($model,'senderEmail',array('style'=>'width: 350px;')); ?>
                <?php echo $form->error($model,'senderEmail'); ?>
            </div>
            
            <div class="row">
                <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend',"To (Friend's email)")?>:</label>
                <?php echo $form->textField($model,'receiverEmail',array('style'=>'width: 350px;')); ?>
                <?php echo $form->error($model,'receiverEmail'); ?>
            </div>
            
            <div class="row">
                <label><?php echo Language::t(Yii::app()->language,'Backend.User.Common',"Message")?>:</label>
                <?php echo $form->textArea($model,'content',array('style'=>'width: 350px; height: 200px;')); ?>
                <?php echo $form->error($model,'content'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model,'verifyCode'); ?>
                <?php echo $form->textField($model,'verifyCode',array('style'=>'width: 150px;','class'=>'extra-text')); ?>
                <?php echo $form->error($model,'verifyCode'); ?>
            </div>
            <div style="clear: both;"></div>
            <div style="margin-left: 158px; margin-top: 0px;">
                <?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'imageOptions'=>array('id'=>'img-captcha'))); ?>
            </div>
            
            <div class="row buttons">
                <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Common.Common','Send'),array('name'=>'btnSend','class'=>'btn')); ?>
            </div>

        <?php $this->endWidget(); ?>    
    <?php endif; ?>
</div>