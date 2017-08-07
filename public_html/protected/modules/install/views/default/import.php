<?php
$languageLookup=ImportForm::getList_languages();
$languageName=$languageLookup[user()->getState('language')];
?>
<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Import Translation file for Language selected</h2>
    <div class="form wide" id="form-language">
    <?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data')); ?>

        <?php echo CHtml::errorSummary($model); ?>
         <?php
/*                foreach(Yii::app()->user->getFlashes() as $key => $message) {
                    echo '<div class="'. $key . 'Message">' . $message . "</div>\n";
                }
                */?>
        <div class="input">
            <?php echo CHtml::activeLabel($model,'lang',array('label'=>'Language')); ?>
            <?php
            echo CHtml::activeDropDownList($model,'lang',array(user()->getState('language')=>$languageName));?>
        </div>
        <div class="input">
        <?php echo CHtml::activeLabel($model,'file'); ?>
        <?php echo CHtml::activeFileField($model, 'file'); ?>
        </div>
        <div class="output">
            <?php echo CHtml::submitButton('Install',array('class'=>'btn')); ?>
            <a href="<?php echo $this->createUrl('default/info'); ?>">Skip this step</a>
        </div>

    <?php echo CHtml::endForm(); ?>
    </div>
</fieldset>