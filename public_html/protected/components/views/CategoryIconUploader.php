
<div id="icon-section">
    <label>Homepage icon</label>
    <?php if ($this->value!='' && $this->value!='none') : ?>
        <div class="current-logo"><?php echo CHtml::image(baseUrl().'/uploads/category/'.$this->value,''); ?>
            <input type="submit" name="removeCurrentLogo" value="Remove Icon" 
                style="position:relative;top:-10px"
                />
        </div>        
    <?php else: ?>
    <div><?php echo CHtml::fileField('categoryIconUploader',''); ?></div>
    <div style="margin-left: 210px;"><input type="submit" id="btn-upload-new-logo" name="uploadNewIcon" value="Upload Icon" /></div>
    <?php endif; ?>
</div>

<script type="text/javascript">
$('#btn-upload-new-logo').click(function(){
    if ($('#categoryIconUploader').val()=='')
    {
        alert('Please select an image file to upload!');
        return false;   
    }
    return true;    
});
</script>