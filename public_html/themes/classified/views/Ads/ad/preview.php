<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');

$breadcrumbItems = array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category') => array('/Ads/ad/selectCategory'),
);
if (!empty($model->id))
 $breadcrumbItems[Language::t(Yii::app()->language,'Frontend.Ads.Common','Update your ad')] = '#';
else
  $breadcrumbItems[Language::t(Yii::app()->language,'Frontend.Ads.Common','Create your ad')] = '#';
$newItems[] = Language::t(Yii::app()->language,'Frontend.Ads.Preview','Preview');
$breadcrumbItems[] = implode(' & ',$newItems);

$this->breadcrumbs = $breadcrumbItems;
?>

<style type="text/css">

</style>

<?php if (!is_null($model)) : ?>
    <h1 class="title-2"><?php echo $model->title; ?></h1>
    <div class="intro">
        <?php
        $adPhotos = array();
        $tempImages = array();
        if (isset($_POST['hdUploadedFiles']) && !empty($_POST['hdUploadedFiles']))
            $tempImages = explode(',',$_POST['hdUploadedFiles']);
        $images = unserialize($model->photos);
        if (count($tempImages) > 0)
        {
            foreach($tempImages as $image)
            {
                $pathImage = 'uploads/ads/temp/'.$image;
                if (file_exists($pathImage))
                    $adPhotos[] = $pathImage;
            }
        }
        if (is_array($images) && count($images) > 0)
        {
            foreach($images as $image)
            {
                $pathImage = 'uploads/ads/'.$image;
                if (file_exists($pathImage))
                    $adPhotos[] = $pathImage;
            }    
        }
        
        if (count($adPhotos) > 0) :
        ?>
            <div class="image">
                <div class="pic">
                    <?php
                    foreach($adPhotos as $pathImage)
                    {
                        $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/image.php?thumb='.FlexImage::createThumbFilename($pathImage,180,135);
                        $imageUrlForFullsize = Yii::app()->request->getBaseUrl(TRUE).'/image.php?path='.$pathImage;
                        echo '<a class="image-item" style="display: none;" rel="image-gallery" href="'.$imageUrlForFullsize.'">
                                <span></span><img src="'.$imageUrl.'" /></a>';
                    }
                    ?>
                </div>
              <div style="clear:both;">
                  <a class="btn-zoom" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','zoom')?></a>
                  <p class="num-img">
                    <a class="btn-pre" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','pre')?></a>
                    <span class="slide-no-container">
                      <span class="current-image-no">1</span> <?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','of')?>
                      <span class="images-total"><?php echo count($adPhotos); ?></span>
                    </span>
                    <a class="btn-next" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','next')?></a>
                  </p>
              </div>
            </div>
            <script type="text/javascript">
            $('.intro .image .pic a.image-item:first').addClass('current').show();
            $('.intro .image a.btn-zoom').attr('href',$('.intro .image .pic a.current').attr('href'));
            $('.intro .image p.num-img a.btn-next').click(function(){
                var current_image = $('.intro .image .pic a.current');
                var next_image = current_image.next();
                if (next_image.length <= 0) return false;
                $('.intro .image .pic a.image-item').removeClass('current').hide();
                next_image.addClass('current').show();
                $('.intro .image a.btn-zoom').attr('href',next_image.attr('href'));
                var curImageNo = parseInt($('.intro .image p.num-img span.current-image-no').html()) + 1;
                $('.intro .image p.num-img span.current-image-no').html(curImageNo);
                return false;    
            });
            $('.intro .image p.num-img a.btn-pre').click(function(){
                var current_image = $('.intro .image .pic a.current');
                var prev_image = current_image.prev();
                if (prev_image.length <= 0) return false;
                $('.intro .image .pic a.image-item').removeClass('current').hide();
                prev_image.addClass('current').show();
                $('.intro .image a.btn-zoom').attr('href',prev_image.attr('href'));
                var curImageNo = parseInt($('.intro .image p.num-img span.current-image-no').html()) - 1;
                $('.intro .image p.num-img span.current-image-no').html(curImageNo);
                return false;    
            });
            $('.intro .image .pic a[rel=image-gallery]').fancybox();
            $('.intro .image a.btn-zoom').fancybox();    
            </script>
        <?php endif; ?>
        <div class="info-product">
            <?php
            $arrLocationParts = array();
            if ($model->area!='')
                $arrLocationParts[] = $model->area;
            if ($model->zipcode!='')
                $arrLocationParts[] = $model->zipcode;
            if (intval(MapSettings::DISPLAY_MAP_ADS)==1  && !empty($model->lat) && !empty($model->lng))
                $arrLocationParts[] = '<a class="lnk-show-map" href="#">'.Language::t(Yii::app()->language,'Backend.Map.Setting','Map').'</a>';
            if (count($arrLocationParts) > 0) :
            ?>    
                <p><strong><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?>:</strong> <?php echo implode(' - ',$arrLocationParts); ?></p>
            <?php endif; ?>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Date posted')?>:</strong> <?php echo Yii::app()->dateFormatter->format('dd MMM yyyy',time()); ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?>:</strong> <?php if (isset($model->arrNotPaymentPriceOptions[$model->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$model->arrNotPaymentPriceOptions[$model->opt_price]); else echo AdsSettings::CURRENCY.' '.$model->price; ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Views')?>:</strong> 0</p>
        </div>
     </div>
     <div class="document">
        <?php
        if(!empty($model->lat) && !empty($model->lng)) :
            Yii::app()->clientScript->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&key='.MapSettings::GAPI,CClientScript::POS_HEAD); ?>
            <script type="text/javascript">
                var lat0 = "<?php echo $model->lat; ?>";
                var lng0 = "<?php echo $model->lng; ?>";
                var zoom = '<?php echo MapSettings::ZOOM_ADS; ?>';
                var map_zoom = '<?php echo MapSettings::MAP_ZOOM_ADS; ?>';
                var map_type = '<?php echo MapSettings::MAP_TYPE_ADS; ?>';
            </script>
            <div class="location-map">
                <a href="#" class="lnk-close-map"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','close X')?></a>
                <div style="width: 600px; height: 400px; clear: both; margin-left: 0px;" id="map_canvas"></div>
            </div>
            <script type="text/javascript">
            $('a.lnk-show-map').click(function(){
                $('.location-map').show();
                initializeGMap();
                return false;    
            });
            $('.location-map a.lnk-close-map').click(function(){
                $('.location-map').hide();
                return false;    
            });
            
            var map = null;
            var geocoder = null;
            // Global stuff
            var mymarker;
            function initializeGMap()
            {
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
                
                var mymarker = new google.maps.Marker({
                    position: myLatlng
                });
                mymarker.setMap(map);
             }
            </script>
        <?php endif; ?>
        <div class="ad-description"><?php echo $model->description; ?></div>    
        <?php if (!empty($model->video)) : ?><div id="ad-video-code" class="ad-video-code"><?php echo $model->video; ?></div><?php endif; ?>
        <script type="text/javascript">
          var videoIframe = $("#ad-video-code iframe");
          var src = videoIframe.attr("src");
          videoIframe.attr("src", src+"?wmode=opaque");
        </script>
     </div>
     <div class="btn-links">
        <form method="post" id="ad-hidden-form">
            <?php
            if (!$model->isNewRecord)
            {
                echo CHtml::activeHiddenField($model,'id');
                echo CHtml::activeHiddenField($model,'photos');
            }
            echo CHtml::activeHiddenField($model,'txn_id');
            echo CHtml::activeHiddenField($model,'category_id');
            echo CHtml::activeHiddenField($model,'title');
            echo CHtml::activeHiddenField($model,'price');
            echo CHtml::activeHiddenField($model,'opt_price');
            echo CHtml::activeHiddenField($model,'description');
            echo CHtml::activeHiddenField($model,'area');
            echo CHtml::activeHiddenField($model,'zipcode');
            echo CHtml::activeHiddenField($model,'lat');
            echo CHtml::activeHiddenField($model,'lng');
            echo CHtml::hiddenField('hdUploadedFiles',$this->post('hdUploadedFiles',''));
            echo CHtml::activeHiddenField($model,'video');
            echo CHtml::activeHiddenField($model,'email');
            echo CHtml::activeHiddenField($model,'featured');
            echo CHtml::activeHiddenField($model,'feature_days');
            echo CHtml::activeHiddenField($model,'feature_total');
            echo CHtml::activeHiddenField($model,'homepage');
            echo CHtml::activeHiddenField($model,'homepage_days');
            echo CHtml::activeHiddenField($model,'homepage_total'); 
            ?>
            <input class="btn" type="submit" value="<?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Edit')?>" name="editUnsavedAd" id="btn-editUnsavedAd" />
            <input class="btn" type="submit" value="<?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Post')?>" name="saveAd" id="btn-saveAd" />
        </form>
     </div>
     
     <script type="text/javascript">
     $('#btn-editUnsavedAd').click(function(){
        $('#ad-hidden-form').attr('action','<?php echo $this->createUrl('/Ads/ad/editUnsavedAd'); ?>');    
     });
     $('#btn-saveAd').click(function(){
        $('#ad-hidden-form').attr('action','<?php echo $this->createUrl('/Ads/ad/performSaveAd'); ?>');
     });
     // breadcrumb
     $('#pageBreadCrumb a:last').click(function(){
        $('#btn-editUnsavedAd').trigger('click');       
     });
     </script>   
<?php else : ?>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad does not exist.')?></p>
<?php endif; ?>
