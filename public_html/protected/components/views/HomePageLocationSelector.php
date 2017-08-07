<?php if (count($locations) > 0) : ?>
    <div class="location-find">
        <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?></label>
        <?php echo CHtml::dropDownList('ddlLocations',$this->selectedLocation,$locations,array('class'=>'location','prompt'=>Language::t(Yii::app()->language,'Backend.Common.Common','All'))); ?>
    </div>
    <script type="text/javascript">
    $('#ddlLocations').change(function(){
        var location = $(this).val();
        var url = '<?php echo Yii::app()->request->getBaseUrl(TRUE); ?>';
        if (location != '')
        {
          location = location.replace(/ /g,"+");
          url = '<?php echo Yii::app()->request->getBaseUrl(TRUE); ?>/'+location+'.htm';///*Yii::app()->createUrl('site/index');*/&location='+location 
        }
        window.location.href = url;     
    });
    </script>
<?php endif; ?>