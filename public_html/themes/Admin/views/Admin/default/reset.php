<!--login. Start-->        
<div class="login">
<h1 style="font-size: 24px;">
    <a href="<?php echo baseUrl(); ?>" style="text-decoration: none; color: #0000ff;">
        <?php
        if (Settings::SITE_LOGO!='' && Settings::SITE_LOGO!='none')
            echo CHtml::image(baseUrl().'/'.'uploads/'.Settings::SITE_LOGO,'Logo',array('class'=>'site-logo', 'width' => 300, 'height' =>84));
        else
            echo Settings::SITE_NAME;
        ?>
    </a>
</h1>
<div class="form wide">
                <?php $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'user-form',
                    'enableAjaxValidation'=>false,
                )); ?>

                    <?php $this->widget('MessageBox'); ?>
                    <?php if(!Yii::app()->user->isGuest): ?>
                    <div>You have just changed password and logged in. Redirecting...</div>
                    <?php endif;?>
                    <div class="row">
                        <label class="c1">Username</label>
                        <div class="c2"><?php echo CHtml::encode($model->email); ?></div>
                    </div>
                    <div class="row">
                        <label class="c1">Full name</label>
                        <div class="c2"><?php echo CHtml::encode($model->username); ?></div>
                    </div>

                    <div class="row">
                        <?php echo $form->labelEx($model,'password',array('class'=>'c1')); ?>
                        <?php echo $form->passwordField($model,'password',array('size'=>40,'maxlength'=>64,'class'=>'c2')); ?>
                        <?php echo $form->error($model,'password'); ?>
                    </div>

                    <div class="row">
                        <?php echo $form->labelEx($model,'confirmPassword',array('class'=>'c1')); ?>
                        <?php echo $form->passwordField($model,'confirmPassword',array('size'=>40,'maxlength'=>64,'class'=>'c2')); ?>
                        <?php echo $form->error($model,'confirmPassword'); ?>
                    </div>

                    <div class="row buttons">
                        <?php echo CHtml::button('Back',array(
                        'onclick'=>'document.location.href="'.Yii::app()->request->urlReferrer.'"',
                        'style'=>'width: 50px;',
                        ));?>
                        <div class="c2"><?php echo CHtml::button('Change Password', array('type'=>'submit')); ?></div>Or
                        <?php echo CHtml::link('Login',array('/Admin/default/login'));?>
                    </div>

                <?php $this->endWidget(); ?>

                </div><!-- form -->
<style type="text/css">
.login .form .buttons input {
    font-size: 11px;
    margin: 0 5px 0 0;
    width: 100px;
}
</style>
<?php
    if(!Yii::app()->user->isGuest) {
        $script ="
        setTimeout(function(){
            url = parent.window.location;
            if (window == parent.window)
                url = '".baseUrl()."';
            parent.window.location = url;
        }, 5000);
        ";
        cs()->registerScript('waiting-login', $script);
    }
?>