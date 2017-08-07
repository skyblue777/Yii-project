<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Ads')=>array('getads'),
);

$this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(Language::t(Yii::app()->language,'Backend.Ads.Get','Get Ads')));
?>
<h1><?php echo Language::t(Yii::app()->language,'Backend.Ads.Get','Get Ads')?></h1>

<?php if($model->value==1) { ?>
<p><?php echo Language::t(Yii::app()->language,'Backend.Ads.Get','Automatic feed enabled')?></p>
<form action="" method="post">
<input type="hidden" name="action" value="<?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Stop')?>" />
<input type="submit" value="Unsubscribe" name="<?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Submit')?>" />
</form>
<?php } else { ?>
<p><?php echo Language::t(Yii::app()->language,'Backend.Ads.Get','Automatically feed your Classifieds site with daily new classified ads.')?> <a href="http://www.titanclassifieds.com/getads.html" target="_blank"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','More details')?></a></p>

<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8RVDZBZF5M5LJ">
<input type="hidden" name="custom" value="url#<?php echo str_replace('http://','',Yii::app()->createAbsoluteUrl('Core/service/index', array('SID' => 'Ads.import.get'))); ?>" />
<!--<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">-->
<input type="submit" value="<?php echo Language::t(Yii::app()->language,'Backend.Ads.Get','Enable automatic feed')?>" name="Submit" />
<!--<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">-->
</form>
<?php } ?>