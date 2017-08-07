<div id="logo-section" style="margin-left: 210px;">
    <?php if ($this->value!='' && $this->value!='none') : ?>
        <div class="current-logo"><?php echo CHtml::image(baseUrl().'/uploads/'.$this->value,''); ?></div>
        <div style="margin-top: 10px;"><input type="submit" name="removeCurrentLogo" value="<?php echo Language::t(Yii::app()->language,"Backend.Ads.Form","Remove")." ".Language::t(Yii::app()->language,"Frontend.Admin.Setting","Logo")?>" /></div>
    <?php endif; ?>
    <div><?php echo CHtml::fileField('siteLogoUploader',''); ?></div>
    <div><input type="submit" id="btn-upload-new-logo" name="uploadNewLogo" value="<?php echo Language::t(Yii::app()->language,"Backend.Common.Common","Upload")." ".Language::t(Yii::app()->language,"Frontend.Admin.Setting","Logo")?>" /></div>
</div>

<script type="text/javascript">
$('#btn-upload-new-logo').click(function(){
    if ($('#siteLogoUploader').val()=='')
    {
        alert('Please select an image file to upload!');
        return false;   
    }
    return true;    
});
</script>