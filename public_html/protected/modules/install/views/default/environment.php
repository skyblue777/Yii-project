<fieldset>
    <h1><a href="#">Titan Classifieds</a></h1>
    <h2>Environment settings</h2>
<?php
if (Yii::app()->user->hasFlash('error') === true) {
    echo '<div class="note"><h4>Error:</h4><p>'.Yii::app()->user->getFlash('error').'</p></div>';
}
?>
    <p>Please enter your database connection details. If you are not sure about these details, please contact your host.</p>
<?php echo CHtml::beginForm();?>
<?php echo CHtml::errorSummary($model, null, null, array('class'=>'note')); ?>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'baseUrl'); ?>
    <?php 
    if ($model->baseUrl == 'http://') $model->baseUrl = Yii::app()->request->getBaseUrl(true);
    echo CHtml::activeTextField($model, 'baseUrl', array('class' => 'text'));
    ?>
    <span class="note">Url to root of your <?php echo Yii::app()->name; ?> site.</span>
</div>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'host'); ?>
    <?php echo CHtml::activeTextField($model, 'host', array('class' => 'text')); ?>
    <span class="note">99% chance you don't need to change this value.</span>
</div>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'port'); ?>
    <?php echo CHtml::activeTextField($model, 'port', array('class' => 'text')); ?>
    <span class="note">99% chance you don't need to change this value.</span>
</div>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'dbName'); ?>
    <?php echo CHtml::activeTextField($model, 'dbName', array('class' => 'text')); ?>
    <span class="note">The name of the database you want to run <?php echo Yii::app()->name; ?> in.</span>
</div>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'username'); ?>
    <?php 
	$model->username = '';
	echo CHtml::activeTextField($model, 'username', array('class' => 'text'));
	?>
    <span class="note">Your MySQL username</span>
</div>
<div class="input">
    <?php echo CHtml::activeLabel($model, 'password'); ?>
    <?php echo CHtml::activeTextField($model, 'password', array('class' => 'text')); ?>
    <span class="note">MySQL password.</span>
</div>
<div class="output">
    <?php echo CHtml::submitButton('Next', array('class'=>'btn')); ?>
</div>
<?php echo CHtml::endForm();?>
</fieldset>