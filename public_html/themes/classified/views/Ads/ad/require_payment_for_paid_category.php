<?php
$this->breadcrumbs = array(
	Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category') => array('/Ads/ad/selectCategory'),
	Language::t(Yii::app()->language,'Frontend.Common.Layout','Pay per ad')
);
?>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Pay per ad') ?></h1>

<?php if (!empty($errorMsg)) : ?>
    <div class="errorMessage" style="margin-left: 0px;"><?php echo $errorMsg; ?></div>
<?php else : ?>
    <span><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Posting an ad in the category you selected requires payment of a fee:') ?>
          <?php echo MoneySettings::PAYPAL_CURRENCY_PAID.' '.MoneySettings::PAID_ADS_PRICE; ?>
    </span>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <div class="btn-links">
          <a class="btn" href="<?php echo $this->createUrl('/Ads/ad/selectCategory'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Previous') ?></a>
          <input type="submit" class="btn" name="submit" value="<?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Continue') ?>" />
      </div>
      <!-- Identify your business so that you can collect the payments. -->
      <input type="hidden" name="business" value="<?php echo MoneySettings::PAYPAL_EMAIL_PAID; ?>" />
      <input type="hidden" name="cmd" value="_xclick" />
      <!-- Specify details about the item that buyers will purchase. -->
      <input type="hidden" name="item_name" value="Pay per Ad" />
      <input type="hidden" name="amount" value="<?php echo MoneySettings::PAID_ADS_PRICE; ?>" />
      <input type="hidden" name="currency_code" value="<?php echo (trim(MoneySettings::PAYPAL_CURRENCY_PAID)=='$')?'USD':MoneySettings::PAYPAL_CURRENCY_PAID; ?>" />
      <input type="hidden" name="custom" value="paid_ads#" />
      
      <!-- Co-Branding & Redirect links -->
      <input type="hidden" name="image_url" value="<?php echo baseUrl().'/uploads/'.Settings::SITE_LOGO; ?>" />
      <?php //if ($ad_id == '') : ?>
      <input type="hidden" name="return" value="<?php echo /*Yii::app()->request->hostInfo.*/Yii::app()->createAbsoluteUrl('Ads/ad/create',array('cat_id'=>$cat->id,'alias'=>$cat->alias)); ?>" />
      <input type="hidden" name="cancel_return" value="<?php echo /*Yii::app()->request->hostInfo.*/Yii::app()->createAbsoluteUrl('/Ads/ad/selectCategory'); ?>" />
      <input type="hidden" name="rm" value="2" />
      <?php //else : ?>
<!--      <input type="hidden" name="return" value="<?php //echo Yii::app()->createAbsoluteUrl('Ads/ad/update', array('cat_id'=>$cat->id,'id'=>$ad_id)); ?>" />-->
<!--      <input type="hidden" name="cancel_return" value="<?php //echo Yii::app()->createAbsoluteUrl('/Ads/ad/selectCategory', array('cat_id'=>$ad_id)); ?>" />-->
      <?php //endif; ?>
<!--      <input type="hidden" name="notify_url" value="<?php //echo Yii::app()->request->hostInfo.url('Ads/ad/paypalNotify'); ?>" />-->
      <input type="hidden" name="no_shipping" value="1" />
      <input type="hidden" name="no_note" value="1" />
    </form>
<?php endif; ?>