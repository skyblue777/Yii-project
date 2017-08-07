<?php if ($message != '<br/>'): ?>
    <div class="<?php echo $this->MessageCssClass ; ?>">
        <?php echo str_replace('<br/>','',$message);?>
    </div>
<?php endif; ?>
<?php if ($errors != '<br/>'): ?>
    <div class="<?php echo $this->ErrorCssClass ; ?>">
        <?php echo str_replace('<br/>',Language::t(Yii::app()->language,'Backend.Common.Message','Please fix the follow errors'),$errors); ?>
    </div>
<?php endif; ?>