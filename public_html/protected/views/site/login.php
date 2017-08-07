<div class="form">
<?php $form=$this->beginWidget('FActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>false,
)); ?>

    <h2><?php echo Language::t(Yii::app()->language,'Backend.Admin.Login','Login with your username and password below:')?></h2>

    <?php $this->widget('MessageBox'); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password'); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

    <div class="row remember">
        <?php echo CHtml::checkBox('remember'); ?>
        <?php echo CHtml::label(Language::t(Yii::app()->language,'Backend.Admin.Login','Remember me on this computer'), 'remember'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Sign in'); ?>
        <?php echo FHtml::link(Language::t(Yii::app()->language,'Backend.Admin.Login','Forgot password'), array('forgot'), array('title' => 'Forgot password')); ?>&nbsp;|&nbsp;<?php echo FHtml::link('Signup', array('signup'), array('title' => 'Signup')); ?>
    </div>

<?php $this->endWidget(); ?>
</div>