<?php if(count($ads) > 0) : ?>
    <?php Yii::app()->clientScript->registerScriptFile(themeUrl().'/scripts/jquery.jcarousel.min.js',CClientScript::POS_HEAD); ?>
    <ul id="homepage-ads-gallery" class="jcarousel-skin-tango">
    <?php foreach($ads as $ad) : ?>
      <?php
       $urlParams = array('id'=>$ad->id,
                          'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
       if ($ad->area != '')
         $urlParams['area'] = $ad->area;
       ?>
        <li>
            <a class="lnk-photo-and-title" title="<?php echo strip_tags($ad->title); ?>"
               href="<?php echo Yii::app()->createUrl('/Ads/ad/viewDetails',$urlParams); ?>">
                <div class="home-ad-photo">
                    <?php
                    $images = unserialize($ad->photos);
                    $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/images/no-image.jpg';
                    if (is_array($images) && count($images) > 0)
                    {
                        $pathImage = 'uploads/ads/'.$images[0];
                        if (file_exists($pathImage))
                            $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/image.php?thumb='.FlexImage::createThumbFilename($pathImage,84,76);
                    }
                    ?>
                    <img src="<?php echo $imageUrl; ?>" />
                </div>
                <div class="home-ad-title"><span><?php echo getFirstWordsFromString($ad->title,2); ?></span></div>
            </a>
            <span class="price"><?php if (isset($ad->arrNotPaymentPriceOptions[$ad->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$ad->arrNotPaymentPriceOptions[$ad->opt_price]); else echo cutString(AdsSettings::CURRENCY.' '.$ad->price,14); ?></span>    
        </li>
    <?php endforeach; ?>
    </ul>
    <p class="more-ad"><a href="<?php echo $this->controller->createUrl('/Ads/ad/selectCategory'); ?>"><strong><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Home','Your ad here')?></strong></a></p>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#homepage-ads-gallery').jcarousel();
    });
    </script>
<?php else : ?>
    <p class="more-ad"></p>
<?php endif; ?>