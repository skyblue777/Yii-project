<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>false,
)); ?>

    <h2>Login with your username and password below:</h2>

    <?php $this->widget('MessageBox'); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username'); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

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

    <div class="row">
        <?php echo $form->labelEx($model,'confirmPassword'); ?>
        <?php echo $form->passwordField($model,'confirmPassword'); ?>
        <?php echo $form->error($model,'confirmPassword'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Sign in'); ?>
        <?php echo FHtml::link('Forgot password', array('forgot'), array('title' => 'Forgot password')); ?>&nbsp;|&nbsp;<?php echo FHtml::link('Login', array('login'), array('title' => 'Login')); ?>
    </div>

<?php $this->endWidget(); ?>
</div>