Hi [<?php echo $ad->email; ?>],<br/>
<br/>
<?php if(trim($model->senderName)!='') echo ucwords($model->senderName).' ['.$model->senderEmail.']'; else echo '['.$model->senderEmail.']'; ?> has replied to your ad (<strong><?php echo $ad->title; ?></strong>) with the following message:<br />
<br/>
<div>
<?php echo nl2br($model->content); ?>
</div>
<br />
<br />
You can click the link below to see this ad :<br /><br />
<?php
$urlParams = array('id'=>$ad->id,
                  'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
if ($ad->area != '')
 $urlParams['area'] = $ad->area;
$adUrl = Yii::app()->createAbsoluteUrl('/Ads/ad/viewDetails',$urlParams); ?>
<a href="<?php echo $adUrl; ?>"><?php echo $adUrl; ?></a>
<br />
<br />
Thanks,<br />
<a href="<?php echo Settings::SITE_URL; ?>"><?php echo Settings::SITE_NAME; ?></a><br />