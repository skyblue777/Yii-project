<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Install database</h2>
<?php
if (Yii::app()->user->hasFlash('error') === true) {
    echo '<div class="note"><h4>Error:</h4><p>'.Yii::app()->user->getFlash('error').'</p></div>';
}
?>
    <?php if (Yii::app()->session->contains('config')===true) :?>
    <p>Can't create environment.php file. Please download <?php echo CHtml::link('this file', array('default/config'));?>, name it environment.php and upload it in the following folder: ./protected/config/</p>
    <p>Then please <?php echo CHtml::link('click here', array('default/build'));?> and try again.</p>
    <?php endif;?>
    
    <?php if ($canConnect): ?>
        <p>We have all information we need. Before continue, note that any existing data in the database you provided will be lost.</p>
        <?php echo CHtml::beginForm();?>
        <!--<div class="input">
            <?php echo CHtml::label('Install example data', 'example', array('style'=>'width:130px'));?>
            <?php echo CHtml::checkBox('example', false);?>
        </div>-->
        <div class="output">
		    <a href="<?php echo $this->createUrl('default/language'); ?>">Back</a>
            <?php  echo CHtml::submitButton('Install', array('name'=>'install', 'class'=>'btn')); ?>
        </div>
        <?php echo CHtml::endForm();?>
    <?php endif; ?>
</fieldset>
