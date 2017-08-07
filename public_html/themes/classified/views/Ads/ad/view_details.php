<style type="text/css">
#main { overflow: visible; }
</style>
<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
$baseUrl = baseUrl();

$breadcrumbs = array();
$parentCat = null;
if (!is_null($adCat))
{
    $breadcrumbs[] = $adCat->title;
    $parentCat = $adCat->parent;    
}

//$location = $this->get('location', '');
$location = Yii::app()->user->getState('location','');
while (!is_null($parentCat) && $parentCat->id != AdsSettings::ADS_ROOT_CATEGORY)
{
    $catParams = array('/Ads/ad/listByCategory',
                       'cat_id'=>$parentCat->id, 
                       'alias'=>$parentCat->alias,);    
    if (!empty($location))
        $catParams['location'] = $location;
    
    $breadcrumbs[$parentCat->title] = $catParams;
    $parentCat = $parentCat->parent;    
}
$breadcrumbs = array_reverse($breadcrumbs);
$this->breadcrumbs = $breadcrumbs;

if (!is_null($model)) : ?>
    <input type="hidden" id="hdAdId" value="<?php echo $model->id; ?>" />
    <?php
    $top_code = '';
    if (MoneySettings::ADSENSE_ADPAGE_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
        $top_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
    elseif (in_array(MoneySettings::BANNER_ADPAGE_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_ADPAGE_CODE) != '')
        $top_code = trim(stripslashes(MoneySettings::BANNER_ADPAGE_CODE));
    if ($top_code != '') :
    ?>
        <div class="banner-ad">
        <?php echo $top_code; ?>      
        </div>
    <?php endif; ?>
    <h1 class="title-2"><?php echo $model->title; ?></h1>
    <div class="intro">
        <?php
        $images = unserialize($model->photos);
        if (is_array($images) && count($images) > 0) :
            $adPhotos = array();
            foreach($images as $image)
            {
                if (file_exists('uploads/ads/'.$image))
                    $adPhotos[] = 'uploads/ads/'.$image;
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
                        echo '<a class="image-item" style="display: none;" rel="image-gallery" href="'.$imageUrlForFullsize.'"><span></span><img src="'.$imageUrl.'" /></a>';
                    }
                    ?>
                </div>
              <div style="clear: both;">
                <a class="btn-zoom" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','zoom')?></a>
                <p class="num-img"><a class="btn-pre" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','pre')?></a>
                  <span class="slide-no-container">
                    <span class="current-image-no">1
                    </span> <?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','of')?> 
                    <span class="images-total"><?php echo count($adPhotos); ?>
                    </span>
                  </span> <a class="btn-next" href="#"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','next')?></a>
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
        <?php endif; endif; ?>
      
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
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Date posted')?></strong> <?php if(!empty($model->create_time) && $model->create_time != '0000-00-00 00:00:00') echo Yii::app()->dateFormatter->format('dd MMM yyyy',strtotime($model->create_time)); ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?>:</strong> <?php if (isset($model->arrNotPaymentPriceOptions[$model->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$model->arrNotPaymentPriceOptions[$model->opt_price]); else echo AdsSettings::CURRENCY.' '.$model->price; ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Ad ID')?>:</strong> <?php echo $model->id; ?></p>
            <?php if (AdsSettings::SHOW_VIEW_COUNTER == 1) : ?>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Views')?>:</strong> <?php echo $model->viewed; ?></p>
            <?php endif; ?>
        </div>
        <p class="reply-email"><a class="large-btn" href="<?php echo $this->createUrl('/Ads/ad/replyToAd',array('id'=>$model->id,'alias'=>str_replace(array(' ','/','\\'),'-',$model->title))); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Reply by email')?></a></p>
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
    <div class="group">
        <?php if (MoneySettings::ADSENSE_ADPAGE_MIDDLE_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '') : ?>
            <div class="banner-ad-2">
            <?php echo trim(stripslashes(MoneySettings::ADSENSE_CODE)); ?>        
            </div>
        <?php endif; ?>
        <ul class="tool">
            <?php
            $urlParams = array('id'=>$model->id,
                              'alias'=>str_replace(array(' ','/','\\'),'-',$model->title));
            if ($model->area != '')
             $urlParams['area'] = $model->area;
            
            $detailsUrl = urlencode($this->createAbsoluteUrl('/Ads/ad/viewDetails',$urlParams)); ?>
            <li>
              <span><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Share this ad:')?></span>
                  <a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo $detailsUrl; ?>"><img src="<?php echo $baseUrl; ?>/images/pic-share-1.jpg" alt="pictute"/></a>
                  <a target="_blank" href="http://twitter.com/intent/tweet?text=<?php echo str_replace('&','',$model->title); ?>&amp;url=<?php echo $detailsUrl; ?>"><img src="<?php echo $baseUrl; ?>/images/pic-share-2.jpg" alt="pictute"/></a>
                  <a target="_blank" href="https://m.google.com/app/plus/x/?v=compose&content=<?php echo str_replace('&','',$model->title); ?> – <?php echo $detailsUrl; ?>"><img src="<?php echo $baseUrl; ?>/images/pic-share-3.jpg" alt="pictute"/></a>
                  <a href="<?php echo $this->createUrl('/Ads/ad/emailAdToFriend',array('id'=>$model->id,'alias'=>str_replace(array(' ','/','\\'),'-',$model->title))); ?>"><img src="<?php echo $baseUrl; ?>/images/pic-share-4.jpg" alt="pictute"/></a>
            </li>
            <li><span><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Print:')?></span> <a class="lnk-print-ad" href="#"><img src="<?php echo $baseUrl; ?>/images/pic-share-5.jpg" alt="pictute"/></a></li>
            <li><span><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Add to favorites:')?></span> <a class="lnk-add-to-favorites" href="#"><img src="<?php echo $baseUrl; ?>/images/pic-share-6.jpg" alt="pictute"/></a></li>
            <li><span><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Report:')?></span> <a class="lnk-report-ad" href="#"><img src="<?php echo $baseUrl; ?>/images/pic-share-7.jpg" alt="pictute"/></a></li>
        </ul>
        <div class="facebook">
            <fb:like href="<?php echo $detailsUrl; ?>" send="true" width="450" show_faces="false"></fb:like>
        </div>
    </div>
    <?php
    $bottom_code = '';
    if (MoneySettings::ADSENSE_ADPAGE_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
        $bottom_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
    elseif (in_array(MoneySettings::BANNER_ADPAGE_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_ADPAGE_CODE) != '')
        $bottom_code = trim(stripslashes(MoneySettings::BANNER_ADPAGE_CODE));
    if ($bottom_code != '') :
    ?>
    <div class="banner-ad">
        <?php echo $bottom_code; ?>    
    </div>
    <?php endif; ?>
    
    <script type="text/javascript">
    $('ul.tool li a.lnk-add-to-favorites').click(function(){
        $.ajax({
            'type' : 'POST',
            'async' : false,
            'url' : baseUrl + '/index.php?r=Core/service/ajax',
            'data' :
            {
                'SID' : 'Ads.Ads.addToFavorites',
                'id' : $('#hdAdId').val()
            },
            'success' : function(json) {
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
                    alert('<?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','This ad was added to your favorites! Thanks!'); ?>');    
                }
            }
        });
        return false;        
    });
    
    $('ul.tool li a.lnk-print-ad').click(function(){
        var url = '<?php echo $this->createUrl('/Ads/ad/viewDetailsAsPrint',array('id'=>$model->id,'alias'=>str_replace(array(' ','/','\\'),'-',$model->title))); ?>';
        window.open(url,'_blank','width=900,height=700');
        return false;    
    });
    
    $('ul.tool li a.lnk-report-ad').click(function(){
        $.ajax({
            'type' : 'POST',
            'async' : false,
            'url' : baseUrl + '/index.php?r=Core/service/ajax',
            'data' :
            {
                'SID' : 'Ads.Ads.report',
                'id' : $('#hdAdId').val(),
                'action': 'spam'
            },
            'success' : function(json) {
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
                    alert('<?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Reported! Thanks!'); ?>');    
                }
            }
        });
        return false;
    });
    </script>   
<?php else : ?>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad does not exist.'); ?></p>
<?php endif; ?>