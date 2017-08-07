<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Select Language:</h2>
    <?php
    if (Yii::app()->user->hasFlash('error') === true) {
        echo '<div class="note"><h4>Error:</h4><p>'.Yii::app()->user->getFlash('error').'</p></div>';
    }
    ?>
    <?php if (Yii::app()->session->contains('config')===true) :?>
    <p>Can't create environment.php file. Please download <?php echo CHtml::link('this file', array('default/config'));?>, name it environment.php and upload it in the following folder: ./protected/config/</p>
    <p>Then please <?php echo CHtml::link('click here', array('default/build'));?> and try again.</p>
    <?php endif;?>

    <?php //if ($canConnect): ?>
    <p>Please select the language of your website (for both Admin Panel and Front end):</p>
    <?php echo CHtml::beginForm();?>
    <!--<div class="input">
            <?php echo CHtml::label('Install example data', 'example', array('style'=>'width:130px'));?>
            <?php echo CHtml::checkBox('example', false);?>
        </div>-->
    <div class="input">
        <?php echo CHtml::label('Language','language');?>
        <?php
        echo CHtml::dropDownList('language',user()->hasState('language')?user()->getState('language'):'',array(
                'en'=>'English (Default)',
                'es'=>'Spanish (Spain)',
                'fr'=>'French (Standard)',
        ));
        ?>
    </div>
    <div class="output">
        <a href="<?php echo $this->createUrl('default/environment'); ?>">Back</a>
        <?php  echo CHtml::submitButton('Next', array('name'=>'next', 'class'=>'btn')); ?>
    </div>
    <?php echo CHtml::endForm();?>
    <?php //endif; ?>
</fieldset>
