<style type="text/css">
ul.qq-upload-list { margin-left: 230px; }
li.qq-upload-success img.delete { cursor: pointer; }
ul.saved-images li { list-style : none; margin-left: 180px; }
</style>

<div class="form wide">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ads-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit'=>true,
    ),
)); ?>

	<p class="note"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Fields with')?> <span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','are required')?></p>

	<?php //echo $form->errorSummary($model); ?>

    <?php if (! $model->isNewRecord) echo $form->hiddenField($model, "id") ; ?>
    
	<div class="row">
        <?php echo $form->labelEx($model,'category_id'); ?>
        <?php $this->widget('CategoryDropDownList', array(
            'model'=>$model,
            'attribute'=>'category_id',
            'root_id' => AdsSettings::ADS_ROOT_CATEGORY,
            'htmlOptions'=>array(
                'prompt'=>'-- '.Language::t(Yii::app()->language,'Backend.Common.Common','Please Select').' --',
            ),
        )); ?>
        <?php echo $form->error($model,'category_id'); ?>
    </div>
    
    <div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('maxlength'=>150,'style'=>'width: 350px;')); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
    
    <div class="row">
        <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?></label>
        <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::PAYMENT_PRICE_OPTION,'uncheckValue'=>NULL)); ?><span style="padding-left: 7px;"><?php echo AdsSettings::CURRENCY; ?></span><?php echo $form->textField($model,'price',array('style'=>'margin-left: 5px;')); ?>
    </div>
    <div class="row">
        <label>&nbsp;</label>
        <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::FREE_PRICE_OPTION,'uncheckValue'=>NULL)); ?><span style="padding-left: 7px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Free')?></span>
    </div>
    <div class="row">
        <label>&nbsp;</label>
        <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::CONTACT_PRICE_OPTION,'uncheckValue'=>NULL)); ?><span style="padding-left: 7px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Please contact')?></span>
    </div>
    <div class="row">
        <label>&nbsp;</label>
        <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::SWAP_TRADE_PRICE_OPTION,'uncheckValue'=>NULL)); ?><span style="padding-left: 7px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Swap / Trade')?></span>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php
        $this->widget('Core.components.tinymce.ETinyMce', array(
            'model'=>$model, 
            'attribute'=>'description', 
            'editorTemplate'=>'custom',
            'width'=>'600px',
            'height'=>'400px',
            'useElFinder'=>false,
            'useCompression'=>false,
        )); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>
    
    <?php
    $lat0 = MapSettings::LATITUDE;
    $lng0 = MapSettings::LONGITUDE;
    $areas = explode(';',AdsSettings::AREA_LIST);
    $locations = array();
    foreach($areas as $key => $area)
    {
        $area = trim($area);
        if (!empty($area))
        {
            $arrAreaParts = explode('|',$area);
            if (count($arrAreaParts)==2)
            {
                $area = trim($arrAreaParts[0]);
                if (($model->isNewRecord && empty($model->area) && $key==0) || (!empty($model->area) && $model->area==$area))
                {
                    $arrLatLng = explode(',',$arrAreaParts[1]);
                    $lat0 = trim($arrLatLng[0]);
                    $lng0 = trim($arrLatLng[1]);
                }    
            }
            $locations[$area] = $area;
        }    
    }
    if (count($locations) > 0) :
    ?>
        <div class="row">
            <?php echo $form->labelEx($model,'area'); ?>
            <?php echo $form->dropDownList($model,'area',$locations); ?>
        </div>
        <script type="text/javascript">
        $('#Annonce_area').change(function(){
            var area = $(this).val();
            $.ajax({
                'type' : 'POST',
                'async' : false,
                'url' : baseUrl + '/index.php?r=Core/service/ajax',
                'data' :
                {
                    'SID' : 'Ads.Map.getLatLngByLocation',
                    'location' : area
                },
                'success' : function(json) {
                    var result = eval(json);
                    if (result.lat!='0' && result.lng!='0')
                    {
                        $('#lat').val(result.lat);
                        $('#lng').val(result.lng);
                        map.setCenter(new google.maps.LatLng(result.lat, result.lng));
                    }    
                }
            });
        });
        </script>
    <?php endif; ?>
    
    <div class="row">
        <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Zip code')?></label>
        <?php echo $form->textField($model,'zipcode',array('maxlength'=>10)); ?>
        <?php echo $form->error($model,'zipcode'); ?>
    </div>
    
    <?php
    if (MapSettings::DISPLAY_MAP_ADS == 1) :
        Yii::app()->clientScript->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&key='.MapSettings::GAPI,CClientScript::POS_HEAD);
    ?>
        <script type="text/javascript">
            var lat0 = "<?php echo $lat0; ?>";
            var lng0 = "<?php echo $lng0; ?>";
            var zoom = '<?php echo MapSettings::ZOOM_ADS; ?>';
            var map_zoom = '<?php echo MapSettings::MAP_ZOOM_ADS; ?>';
            var map_type = '<?php echo MapSettings::MAP_TYPE_ADS; ?>';
        </script>
        
        <div class="google-map">
            <?php
            echo $form->hiddenField($model,'lat',array('id'=>'lat'));
            echo $form->hiddenField($model,'lng',array('id'=>'lng'));
            ?>
            <div style="width: 600px; height: 400px; clear: both; margin-left: 210px;" id="map_canvas"></div>    
        </div>
        <script type="text/javascript">
        var map = null;
        var geocoder = null;
        // Global stuff
        var mymarker;

        function initializeGMap()
        {
            var lat = $('#lat').val();
            var lng = $('#lng').val();
            if(lat!=0 && lng!=0)
            {
                lat0=lat;
                lng0=lng;
            }
            
            var map_type_id = google.maps.MapTypeId.ROADMAP;
            if (map_type==2) map_type_id = google.maps.MapTypeId.SATELLITE;
            else if (map_type==3) map_type_id = google.maps.MapTypeId.HYBRID;
            
            var myLatlng = new google.maps.LatLng(lat0, lng0);
            var myOptions = {
              zoom: parseInt(zoom),
              center: myLatlng,
              mapTypeId: map_type_id
            };
            
            map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
            
            if(lat!=0 && lng!=0)
            {
                var marker = new google.maps.Marker({
                    position: myLatlng
                });
                marker.setMap(map);
                mymarker = marker;
            }
            
            google.maps.event.addListener(map, 'click', function(event) {
                if (mymarker) mymarker.setMap(null);
                var marker = new google.maps.Marker({
                    position: event.latLng
                });
                marker.setMap(map);
                mymarker = marker;
                $('#lat').val(event.latLng.lat());
                $('#lng').val(event.latLng.lng());
            });
        }
      initializeGMap();
        </script>
    <?php endif; ?>
    
    <?php if(!$model->isNewRecord && !empty($model->photos)) : ?>
        <?php
        echo $form->hiddenField($model,'photos');
        $arrPhotos = unserialize($model->photos);
        if (is_array($arrPhotos) && count($arrPhotos) > 0) :
        ?>
        <div class="row" id="ad-images-section">
            <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Current Images')?></label>
            <?php foreach($arrPhotos as $photo) :
                $imagePath = 'uploads/ads/'.$photo;
            ?>
            <ul class="saved-images">
                <li>
                    <img width="100px" height="100px" src="<?php echo $imagePath; ?>" />
                    <a href="#" id="remove-img-<?php echo $photo; ?>" class="lnk-remove-img"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Form','Remove')?></a>
                </li>
            </ul>
            <?php endforeach; ?>
        </div>
        <script type="text/javascript">
        $('ul.saved-images li a.lnk-remove-img').click(function(){
            var image_name = $(this).attr('id').replace('remove-img-','');
            var ad_id = $('#Annonce_id').val();
            var image_item = $(this).parent();
            if (!confirm('<?php echo Language::t(Yii::app()->language,'Backend.Ads.Form','Are you sure you want to remove this photo?')?>')) return false;
            $.post(
                baseUrl + '/index.php?r=Core/service/ajax',
                {
                    'SID' : 'Ads.Ads.deleteUploadedPhoto',
                    'ad_id' : ad_id,
                    'photo_name' : image_name
                },
                function(json)
                {
                    var result = eval(json);
                    if (result.errors.ErrorCode)
                    {
                        var error = '';
                        for(var i in result.errors.ErrorCode)
                            error += result.errors.ErrorCode[i] + "\n\n";
                        alert(error);
                    }
                    else
                    {
                        image_item.remove();
                        // neu khong con hinh nao thi xoa luon div deal-image-section
                        if ($('#ad-images-section ul.saved-images li').length <= 0)
                            $('#ad-images-section').remove();  
                    }
                }
            );
            return false;    
        })
        </script>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="row">
        <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Upload Images')?></label>
        <input type="hidden" id="hdUploadedFiles" name="hdUploadedFiles" />
        <?php
        $this->widget('application.extensions.EAjaxUpload.EAjaxUpload',
            array(                                        
                'id'=>'imageUploader',
                'config'=>array(
                    'action'=>Yii::app()->createUrl('/Ads/Ads/uploadImage'),
                    'allowedExtensions'=>array('jpg','png','jpeg','gif','bmp'),
                    'sizeLimit'=>1*1024*1024,
                    'minSizeLimit'=>0,
           	 		'template'=>'<div class="qq-uploader">
            					<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>
            					<div class="qq-upload-button btn">'.Language::t(Yii::app()->language,'Frontend.Common.Common','Select').'</div><ul class="qq-upload-list"></ul></div>',
                    'onSubmit'=>"js:function(id, fileName, responseJSON){
                       // find saved images
                       var maxFile = ".intval(AdsSettings::PHOTO_MAX_COUNT).";
                       var fileCount = $('.qq-uploader ul.qq-upload-list li.qq-upload-success').length + $('ul.saved-images li').length;
                       if (fileCount >= maxFile)
                       {
                            alert(\"".Language::t(Yii::app()->language,'Backend.Common.Message','You can only upload a maximum of')." \"+maxFile+\" ".Language::t(Yii::app()->language,'Backend.Common.Common','Photos')."!\");
                            return false;
                       }
                    }    
                    ",
                    'onComplete'=>"js:function(id, fileName, responseJSON){
                        var result = eval(responseJSON);
                        $('.qq-upload-list li:last span.qq-upload-file').html(result.filename);
                        var str = $('#hdUploadedFiles').val();
                        if (str=='')
                            str = result.filename;
                        else
                            str = str + ',' + result.filename;
                        $('#hdUploadedFiles').val(str);
                        $('.qq-upload-list li:last').append('<img class=\"delete\" src=\"images/delete.png\" /><br />');                                        
                    }",                                        
                )
            ));
        ?>    
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'video'); ?>
        <?php echo $form->textField($model,'video',array('style'=>'width: 350px;')); ?>
        <?php echo $form->error($model,'video'); ?>
    </div>
    
    <?php if ($model->isNewRecord) : ?>
        <div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email',array('style'=>'width: 350px;')); ?>
            <?php echo $form->error($model,'email'); ?>
        </div>
    <?php else : ?>
        <div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <span style="line-height: 24px;"><?php echo $model->email; ?></span>
        </div>    
    <?php endif; ?>

	<div style="margin-left: 210px;">
		<?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Common.Common','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>

<script type="text/javascript">
if ($('div.body-container').find('div:first').attr('class') == 'errorMessage')
{
    $('div.body-container').find('div:first').remove();   
}

$('#imageUploader li.qq-upload-success img.delete').live('click',function(){
    var _this=$(this);  
    _this.attr('src','images/ajax-loader.gif');              
    var fileName=_this.parent().find("span.qq-upload-file").text();
    $.post(
        baseUrl+"/index.php?r=Core/service/ajax",
        {
            SID : 'Ads.Ads.deleteFileInTemp',
            fileName : fileName,
            uploadedFiles : $('#hdUploadedFiles').val()                        
        },
        function(json){
            var result = eval(json);                        
            _this.closest('li').remove();
            $('#hdUploadedFiles').val(result.uploadedFiles);
        }
    ); 
});
</script>