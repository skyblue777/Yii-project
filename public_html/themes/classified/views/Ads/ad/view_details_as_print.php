<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');

//$baseUrl = baseUrl();

$breadcrumbs = array();
$parentCat = null;
if (!is_null($adCat))
{
    $breadcrumbs[] = $adCat->title;
    $parentCat = $adCat->parent;    
}

while (!is_null($parentCat) && $parentCat->id != AdsSettings::ADS_ROOT_CATEGORY)
{    
    $breadcrumbs[] = $parentCat->title;    
    $parentCat = $parentCat->parent;    
}
$breadcrumbs = array_reverse($breadcrumbs);
$this->breadcrumbs = $breadcrumbs;
?>
<?php if (!is_null($model)) : ?>
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
                        echo '<a class="image-item" style="display: none;" rel="image-gallery" href="'.$imageUrlForFullsize.'"><img src="'.$imageUrl.'" /></a>';
                    }
                    ?>
                </div>
                <a class="btn-zoom" href="#">zoom</a>
                <p class="num-img"><a class="btn-pre" href="#">pre</a> <span><span class="current-image-no">1</span> of <span class="images-total"><?php echo count($adPhotos); ?></span></span> <a class="btn-next" href="#">next</a></p>
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
            if (count($arrLocationParts) > 0) :
            ?>    
                <p><strong><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?>:</strong> <?php echo implode(' - ',$arrLocationParts); ?></p>
            <?php endif; ?>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Date posted')?></strong> <?php if(!empty($model->create_time) && $model->create_time != '0000-00-00 00:00:00') echo Yii::app()->dateFormatter->format('dd MMM yyyy',strtotime($model->create_time)); ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?>:</strong> <?php if (isset($model->arrNotPaymentPriceOptions[$model->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$model->arrNotPaymentPriceOptions[$model->opt_price]); else echo AdsSettings::CURRENCY.' '.$model->price; ?></p>
            <p><strong><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Ad ID')?>:</strong> <?php echo $model->id; ?></p>
        </div>
    </div>
    <div class="document">
        <?php echo $model->description; ?>
    </div>
    <?php if (MoneySettings::ADSENSE_ADPAGE_MIDDLE_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '') : ?>
        <div class="banner-ad-2">
        <?php echo trim(stripslashes(MoneySettings::ADSENSE_CODE)); ?>        
        </div>
    <?php endif; ?>
    
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
    window.print();
    </script>   
<?php else : ?>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad does not exist.'); ?></p>
<?php endif; ?>