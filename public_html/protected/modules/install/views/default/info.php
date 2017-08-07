<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Site settings</h2>
    <p>Please provide the following information. Don’t worry, you can always change these settings later.</p>
    <?php echo CHtml::beginForm(); ?>
    <?php echo CHtml::errorSummary($model, null, null, array('class'=>'note')); ?>
    <div class="input">
        <?php echo CHtml::activeLabel($model, 'siteName'); ?>
        <?php echo CHtml::activeTextField($model, 'siteName', array('class' => 'text')); ?>
    </div>
    <div class="input">
        <?php echo CHtml::activeLabel($model, 'adminEmail'); ?>
        <?php echo CHtml::activeTextField($model, 'adminEmail', array('class' => 'text')); ?>
        <span class="note">Double-check your email address before continuing</span>
    </div>
    <div class="output">
        <?php echo CHtml::submitButton('Install', array('class'=>'btn')); ?>
    </div>
    <?php echo CHtml::endForm();?>
</fieldset>