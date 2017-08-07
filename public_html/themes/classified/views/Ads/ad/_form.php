<style type="text/css">
ul.qq-upload-list { margin-left: 126px; }
li.qq-upload-success img.delete { cursor: pointer; }
ul.saved-images li { list-style : none; margin-left: 100px; }
.qq-uploader .btn { font-size: 1em; }
.create-ads label { width: 105px; }
.create-ads .buttons { padding-left: 103px; }
.create-ads .group-2 { margin-left: 113px; }
.create-ads .errorMessage { margin-left: 113px; }
input#fake_price { background-color: lightgrey; border-color: lightgrey; }
</style>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'ads-form',
    'action'=>array('/Ads/ad/preview'),
    //'enableAjaxValidation'=>true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit'=>true,
    ),
));

// echo $form->errorSummary($model);

if (!$model->isNewRecord) echo $form->hiddenField($model, "id") ;
echo $form->hiddenField($model, "txn_id");
$model->setScenario('edit_ad_price');
?>
    
	<div class="row">
        <?php echo $form->hiddenField($model,'category_id'); ?>
        <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Category')?>:</label>
        <div class="group-1"><?php echo $cat->parent->title; ?> / <?php echo $cat->title; ?> 
        <?php if ($model->isNewRecord) : ?>
          <a class="change" href="<?php echo $this->createUrl('/Ads/ad/selectCategory') ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Category','Change category')?></a>
        <?php //else : ?>
<!--          <a class="change" href="<?php //echo $this->createUrl('/Ads/ad/selectCategory', array('ad_id'=>$model->id)); ?>">Change category</a>-->
        <?php endif; ?>
        </div>
  </div>    
  <div class="row">
		<label class="required" for="Annonce_title"><span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Title')?>:</label>
		<?php 
    echo $form->textField($model,'title',array('maxlength'=>100,'style'=>'width: 350px;'));
		echo $form->error($model,'title'); 
    ?>
	</div>
  <?php if ($model->isNewRecord && $cat->price_required == 1) : ?>
  <div class="row">
		<label class="required" for="Annonce_price"><span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?>:</label>
    <label style="width: auto; padding-top: 3px;"><?php echo AdsSettings::CURRENCY; ?></label>
		<?php 
    echo $form->textField($model,'price');
		echo $form->error($model,'price');
    echo $form->radioButton($model,'opt_price',
                            array('value'=>Annonce::PAYMENT_PRICE_OPTION,
                                  'uncheckValue'=>NULL, 'checked'=>'true',
                                  'style'=>'display: none;'));
    ?>
	</div>
  <?php else : ?>
  <div class="row">
      <label style="padding-top: 4px;"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?>:</label>
      <div class="group-1">
          <ul class="check-list">
              <li>
                <div id="price">
                  <?php
                  echo $form->radioButton($model,'opt_price',
                                          array('value'=>Annonce::PAYMENT_PRICE_OPTION,
                                                'uncheckValue'=>NULL,
                                                'style'=>'margin-top: 4px;'));
                  ?>
                  <label style="width: auto; padding-top: 3px;"><?php echo AdsSettings::CURRENCY; ?></label>
                  <input type="text" id="fake_price" <?php echo (($model->opt_price == Annonce::PAYMENT_PRICE_OPTION)?'style="display:none;"':'');?> disabled="true" value="" />
                  <?php
                  if ($model->opt_price == Annonce::PAYMENT_PRICE_OPTION)
                    echo $form->textField($model,'price');
                  else
                    echo $form->textField($model,'price',array('style'=>'display:none;','value'=>'1'));
                  echo $form->error($model,'price');
                  ?>
                </div>
              </li>
              <li>
                  <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::FREE_PRICE_OPTION,'uncheckValue'=>NULL)); ?>
                  <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Free')?></label>
              </li>
              <li>
                  <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::CONTACT_PRICE_OPTION,'uncheckValue'=>NULL)); ?>
                  <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Please contact')?></label>
              </li>
              <li>
                  <?php echo $form->radioButton($model,'opt_price',array('value'=>Annonce::SWAP_TRADE_PRICE_OPTION,'uncheckValue'=>NULL)); ?>
                  <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Swap / Trade')?></label>
              </li>
          </ul>
      </div>
  </div>
<script type="text/javascript">
  $('input#Annonce_opt_price').change(function() {
    if ($(this).val() == '<?php echo Annonce::PAYMENT_PRICE_OPTION ?>')
    {
      //var price = $('#price input#fake_price').val();
      $('#price input#Annonce_price').removeAttr('style');
      $('#price input#Annonce_price').attr('value', ''); //option(price)
      $('#price input#fake_price').attr('style', 'display:none;');
    } else {
      //var price = $('#price input#Annonce_price').val();
      //$('#price input#fake_price').attr('value', price);
      $('#price input#fake_price').removeAttr('style');
      $('#price input#Annonce_price').attr('style', 'display:none;');
      $('#price input#Annonce_price').attr('value', '1');
      $('#price input#Annonce_price').blur();
    }
  });
</script>
  <?php endif; ?>

  <div class="row">
      <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Description')?>:</label>
      <?php
      $this->widget('Core.components.tinymce.ETinyMce', array(
          'model'=>$model, 
          'attribute'=>'description', 
          'editorTemplate'=>'custom',
          'width'=>'800px',
          'height'=>'400px',
          'useCompression'=>false,
          'useElFinder'=>false,
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
            <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?>:</label>
            <div class="group-3">
                <?php echo $form->dropDownList($model,'area',$locations); ?>
                <label class="lbl-2"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Zip code')?>:</label>
                <?php echo $form->textField($model,'zipcode',array('maxlength'=>10,'class'=>'text-3')); ?>
                <?php echo $form->error($model,'zipcode'); ?>
            </div>
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
    <?php else : ?>
        <div class="row">
            <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Zip code')?></label>
            <?php echo $form->textField($model,'zipcode',array('maxlength'=>10,'class'=>'text-3')); ?>
            <?php echo $form->error($model,'zipcode'); ?>
        </div>
    <?php endif; ?>
    
    <?php
    if (MapSettings::DISPLAY_MAP_ADS == 1) :
        Yii::app()->clientScript->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&key='.MapSettings::GAPI,CClientScript::POS_HEAD);
        //Yii::app()->clientScript->registerScriptFile('http://maps.google.com/maps?file=api&v=2&key='.MapSettings::GAPI,CClientScript::POS_HEAD);
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
            <div style="width: 600px; height: 400px; clear: both; margin-left: 113px; margin-bottom: 10px;" id="map_canvas"></div>    
        </div>
        <script type="text/javascript">
        var map = null;
        var geocoder = null;
        // Global stuff
        var mymarker;
        //var locMarker;
        /*var icon = new GIcon();
            icon.image = "images/inkon.png";
            icon.iconSize = new GSize(32, 39);
            icon.shadowSize = new GSize(37, 34);
            icon.iconAnchor = new GPoint(9, 34);
            icon.infoWindowAnchor = new GPoint(9, 2);
        var MyIcon = new GIcon();
            MyIcon.image = "images/inkon2.png";
            MyIcon.iconSize = new GSize(19, 32);
            MyIcon.shadowSize = new GSize(37, 34);
            MyIcon.iconAnchor = new GPoint(9, 34);
            MyIcon.infoWindowAnchor = new GPoint(9, 2);*/

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
                  zoomControl: (map_zoom=='1') ? true : false,
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

                /*if (GBrowserIsCompatible())
                {
                    map = new GMap2(document.getElementById("map_canvas"));
                    map.setCenter(new GLatLng(lat0, lng0), 11);
                    map.setZoom(zoom);
                    if (map_zoom) map.addControl(new GSmallMapControl());
                    if (map_type==2) map.setMapType(G_SATELLITE_MAP);
                    else if (map_type==3) map.setMapType(G_HYBRID_MAP);
                    else map.setMapType(G_NORMAL_MAP);
                    map.addControl(new GMapTypeControl());
                    geocoder = new GClientGeocoder();

                    if(lat!=0 && lng!=0)
                    {
                        var locpoint= new GLatLng(lat,lng);
                        locMarker = new GMarker(locpoint);
                        map.addOverlay(locMarker);
                    }
                }*/
             // showAddress("Wilkie road, Singapore","Inkiti :\n");

                /*GEvent.addListener(map, 'click', function( overlay, point )
                {
                    if (locMarker)
                        map.removeOverlay(locMarker);
                    if (mymarker)
                        map.removeOverlay(mymarker);
                    if (point)
                    {
                         map.panTo(point);
                         mymarker = new GMarker(point);
                         map.addOverlay(mymarker);
                         document.getElementById("lat").value=point.y;
                         document.getElementById("lng").value=point.x;
                    }
                    latLon = point;
                });*/
             }
             initializeGMap();
        </script>
    <?php endif; ?>
    
    <?php if(!$model->isNewRecord && !empty($model->photos)) : ?>
        <?php
        //echo $form->hiddenField($model,'photos');
        $arrPhotos = unserialize($model->photos);
        if (is_array($arrPhotos) && count($arrPhotos) > 0) :
        ?>
        <div class="row" id="ad-images-section">
            <label>Current Images:</label>
            <ul class="saved-images">
            <?php foreach($arrPhotos as $photo) :
                $imagePath = 'uploads/ads/'.$photo;
            ?>
                <li>
                    <img width="100px" height="100px" src="<?php echo baseUrl().'/'.$imagePath; ?>" />
                    <a href="#" id="remove-img-<?php echo $photo; ?>" class="lnk-remove-img"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Form','Remove')?></a>
                </li>
            <?php endforeach; ?>
            </ul>
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
        });
        </script>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="row" style="padding: 0;">
        <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Images')?>:</label>
        <input type="hidden" id="hdUploadedFiles" name="hdUploadedFiles" value="<?php if(Yii::app()->request->isPostRequest) echo $this->post('hdUploadedFiles',''); ?>" />
        <?php
        $this->widget('application.extensions.EAjaxUpload.EAjaxUpload',
            array(                                        
                'id'=>'imageUploader',
                'filesName'=>$this->post('hdUploadedFiles',''),
                'config'=>array(
                    'action'=>Yii::app()->createUrl('/Ads/Ad/uploadImage'),
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
                        $('.qq-upload-list li:last').append('<img class=\"delete\" src=\"".baseUrl()."/"."images/delete.png\" /><br />');                                        
                        $('.qq-upload-list li:last span.qq-upload-size').remove();
                    }",                                        
                )
            ));
        ?>    
    </div>
    
    <div class="row" style="padding: 0 0 5px;">
        <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Video')?>:</label>
        <?php echo $form->textField($model,'video',array('style'=>'width: 350px;')); ?>
        <span class="note-text"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Copy the embed tag of the video')?></span>
        <?php echo $form->error($model,'video'); ?>
    </div>
    
    <?php if ($model->isNewRecord && Yii::app()->user->isGuest) : ?>
        <div class="row">
            <label class="required" for="Annonce_email"><span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Email')?>:</label>
            <?php echo $form->textField($model,'email',array('style'=>'width: 350px;')); ?>
            <span class="note-text"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Your email address will not be shared with others')?></span>
            <?php echo $form->error($model,'email'); ?>
        </div>
    <?php else :
        if (!Yii::app()->request->isPostRequest && $model->isNewRecord && !Yii::app()->user->isGuest)
            $model->email = Yii::app()->user->email;
    ?>
        <div class="row">
            <label class="required" for="Annonce_email"><span class="required">*</span> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Email')?>:</label>
            <span style="line-height: 24px;"><?php echo $model->email; ?></span>
            <?php echo $form->hiddenField($model,'email'); ?>
        </div>    
    <?php endif; ?>
    
    <?php if (AdsSettings::TOP_ADS == 1 || AdsSettings::HG == 1) : ?>
    <?php if (intval(MoneySettings::TOP_TIME1) > 0 || intval(MoneySettings::TOP_TIME2) > 0 || intval(MoneySettings::HG_TIME1) > 0 || intval(MoneySettings::HG_TIME2) > 0) : ?>
        <div class="row">
            <p class="type"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Promote my ad (optional)')?></p>
            <div class="group-2">
                <ul class="promote-list">
                    <?php if (AdsSettings::TOP_ADS == 1 && (intval(MoneySettings::TOP_TIME1) > 0 || intval(MoneySettings::TOP_TIME2) > 0)) : ?>
                        <li>
                            <div class="col-1">
                                <?php echo $form->checkBox($model,'featured',array('class'=>'chk-promote-ad')); ?>
                                <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Top Ad')?></label>
                            </div>
                            <div class="col-2">
                                <?php
                                $arrTopAdDays = array();
                                if (intval(MoneySettings::TOP_TIME1) > 0) $arrTopAdDays[MoneySettings::TOP_TIME1] = MoneySettings::TOP_TIME1.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days');
                                if (intval(MoneySettings::TOP_TIME2) > 0) $arrTopAdDays[MoneySettings::TOP_TIME2] = MoneySettings::TOP_TIME2.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days');
                                echo $form->dropDownList($model,'feature_days',$arrTopAdDays,array('class'=>'numday'));
                                ?>
                            </div>
                            <div class="col-3">
                                <?php echo MoneySettings::PAYPAL_CURRENCY_TOP; ?> <span id="top-ad-promote-price"><?php echo $model->feature_total; ?></span>
                                <?php echo $form->hiddenField($model,'feature_total'); ?>
                            </div>
                            <div class="text" style="color: #999999;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Place your ad in the Top Ads section located above all other Ads')?></div>
                        </li>
                        <script type="text/javascript">
                        var arrTopAdDayPrice = new Array();
                        <?php if (intval(MoneySettings::TOP_TIME1) > 0) : ?>
                            arrTopAdDayPrice['<?php echo MoneySettings::TOP_TIME1; ?>'] = '<?php echo intval(MoneySettings::TOP_PRICE1); ?>';
                        <?php endif; ?>
                        <?php if (intval(MoneySettings::TOP_TIME2) > 0) : ?>
                            arrTopAdDayPrice['<?php echo MoneySettings::TOP_TIME2; ?>'] = '<?php echo intval(MoneySettings::TOP_PRICE2); ?>';
                        <?php endif; ?>
                        $('#Annonce_feature_days').change(function(){
                            var day = $(this).val();
                            var price = arrTopAdDayPrice[day];
                            $('#Annonce_feature_total').val(price);
                            $('#top-ad-promote-price').html(price);    
                        });
                        </script>
                    <?php endif; ?>
                    <?php if (AdsSettings::HG == 1 && (intval(MoneySettings::HG_TIME1) > 0 || intval(MoneySettings::HG_TIME2) > 0)) : ?>
                        <li>
                            <div class="col-1">
                                <?php echo $form->checkBox($model,'homepage',array('class'=>'chk-promote-ad')); ?>
                                <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Menu','Homepage Gallery')?></label>
                            </div>
                            <div class="col-2">
                                <?php
                                $arrHomePageAdDays = array();
                                if (intval(MoneySettings::HG_TIME1) > 0) $arrHomePageAdDays[MoneySettings::HG_TIME1] = MoneySettings::HG_TIME1.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days');
                                if (intval(MoneySettings::HG_TIME2) > 0) $arrHomePageAdDays[MoneySettings::HG_TIME2] = MoneySettings::HG_TIME2.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days');;
                                echo $form->dropDownList($model,'homepage_days',$arrHomePageAdDays,array('class'=>'numday'));
                                ?>
                            </div>
                            <div class="col-3">
                                <?php echo MoneySettings::PAYPAL_CURRENCY_HG; ?> <span id="homepage-promote-price"><?php echo $model->homepage_total; ?></span>
                                <?php echo $form->hiddenField($model,'homepage_total'); ?>
                            </div>
                            <div class="text" style="color: #999999;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Place your Ad on the Homepage gallery.')?></div>
                        </li>
                        <script type="text/javascript">
                        var arrHomeAdDayPrice = new Array();
                        <?php if (intval(MoneySettings::HG_TIME1) > 0) : ?>
                            arrHomeAdDayPrice['<?php echo MoneySettings::HG_TIME1; ?>'] = '<?php echo intval(MoneySettings::HG_PRICE1); ?>';
                        <?php endif; ?>
                        <?php if (intval(MoneySettings::HG_TIME2) > 0) : ?>
                            arrHomeAdDayPrice['<?php echo MoneySettings::HG_TIME2; ?>'] = '<?php echo intval(MoneySettings::HG_PRICE2); ?>';
                        <?php endif; ?>
                        $('#Annonce_homepage_days').change(function(){
                            var day = $(this).val();
                            var price = arrHomeAdDayPrice[day];
                            $('#Annonce_homepage_total').val(price);
                            $('#homepage-promote-price').html(price);
                        });
                        </script>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <script type="text/javascript">
        $('input.chk-promote-ad').click(function(){
            var eid = $(this).attr('id');
            if ($(this).is(':checked'))
            {
                if (eid=='Annonce_featured')
                {
                    if ($('#Annonce_homepage').length > 0) $('#Annonce_homepage').attr('checked', false);
                }
                else
                {
                    if ($('#Annonce_featured').length > 0) $('#Annonce_featured').attr('checked', false);    
                }    
            }    
        });
        </script>
    <?php endif; ?>
    <?php endif; ?>

	<div class="row buttons">
        <?php if (!$model->isNewRecord) : ?>
            <a id="lnk-delete-ad" class="btn" href="#"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Delete')?></a>
            <script type="text/javascript">
            $('#lnk-delete-ad').click(function(){
                if (!confirm('<?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','Do you want to delete this ad ?')?>')) return false;
                window.location.href = "<?php if (!Yii::app()->user->isGuest) echo $this->createUrl('/Ads/ad/delete',array('id'=>$model->id)); else echo $this->createUrl('/Ads/ad/delete',array('id'=>$model->id,'email'=>$model->email,'code'=>$model->code)); ?>";    
            });
            </script>
        <?php endif; ?>
		<?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Frontend.Ads.Form','Post / Preview'),array('name'=>'btnPreviewAd','class'=>'btn')); ?>
        <span class="note-text"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form',"By postion your ad, you're agreeing to our ")?><a id="lnk-view-terms_of_use" href="#"> <?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form',"terms of use")?></a>.</span>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
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
<?php 
switch (Yii::app()->language){
case 'en':
	$alias='terms-of-use';
	break;
case 'fr':
	$alias='conditions-dutilisation';
	break;
case 'es':
	$alias='condiciones-de-uso';
	break;
default:
	$alias='terms-of-use-'.Yii::app()->language;
}
?>
$('#lnk-view-terms_of_use').click(function(){
    window.open('<?php echo url('/site/support', array('alias' => $alias)); ?>','_blank','width=980,height=700,scrollbars=yes');
    return false;    
});
</script>