Hello,<br />
<br />
<?php echo '<a href="mailto:'.$model->senderEmail.'">'.$model->senderEmail.'</a>'; ?> wants you to look at this Classified Ad: "<?php echo $ad->title; ?>"
<br />
<br />
<?php
$urlParams = array('id'=>$ad->id,
                   'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
if ($ad->area != '')
  $urlParams['area'] = $ad->area;
 
$adUrl = baseUrl().Yii::app()->createUrl('/Ads/ad/viewDetails',$urlParams); ?>

View this Ad : <a href="<?php echo $adUrl; ?>"><?php echo $adUrl; ?></a>
<?php if($model->content!='') : ?>
    <br />
    <br />Message:
    <br />
    <br />
    <div style="margin-left: 10px;">
        <?php echo nl2br($model->content); ?>
    </div>
<?php endif; ?>
<br />
<br />
Thanks,<br />
<a href="<?php echo baseUrl(); ?>"><?php echo Settings::SITE_NAME; ?></a><br />