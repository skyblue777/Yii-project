<ul id="lastest-ads" class="list">
<?php foreach($lastestAds as $ad) : ?>
    <li>
        <?php
        $images = unserialize($ad->photos);
        $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/images/no-image.jpg';
        if (is_array($images) && count($images) > 0)
        {
            $pathImage = 'uploads/ads/'.$images[0];
            if (file_exists($pathImage))
                $imageUrl = Yii::app()->request->getBaseUrl(TRUE).'/image.php?thumb='.FlexImage::createThumbFilename($pathImage,65,62);
        }
        ?>
        <div class="ad-photo-container"><div class="photo-contain"><img class="ad-photo" src="<?php echo $imageUrl; ?>" /></div></div>
        <?php
         $urlParams = array('id'=>$ad->id,
                            'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
         if ($ad->area != '')
           $urlParams['area'] = $ad->area;
         ?>
        <div class="description">
            <p class="title-item">
              <a href="<?php echo Yii::app()->createUrl('/Ads/ad/viewDetails',$urlParams); ?>">
                <?php echo getFirstWordsFromString($ad->title, 3); ?>
              </a>
            </p>
            <p class="short-desc"><?php echo getFirstWordsFromString(strip_tags($ad->description),10); ?></p>
            <p class="price"><?php if (isset($ad->arrNotPaymentPriceOptions[$ad->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$ad->arrNotPaymentPriceOptions[$ad->opt_price]); else echo AdsSettings::CURRENCY.' '.$ad->price; ?></p>
            <p class="hours"><?php if (!empty($ad->create_time) && $ad->create_time != "0000-00-00 00:00:00") echo Language::getInterval(strtotime($ad->create_time)); ?></p>
        </div>
    </li>
<?php endforeach; ?>
</ul>