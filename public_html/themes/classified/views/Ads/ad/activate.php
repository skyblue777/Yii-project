<div class="mesg">
<?php if (empty($errorMsg)) : ?>
    <h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Message','Congratulations!')?></h1>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Active','Your ad')?> "<?php echo $model->title; ?>
      " <?php echo Language::t(Yii::app()->language,'Frontend.Ads.Active','has been activated. You can click')?>
      <?php
      $urlParams = array('id'=>$model->id,
                         'alias'=>str_replace(array(' ','/','\\'),'-',$model->title));
      if ($model->area != '')
        $urlParams['area'] = $model->area;
      ?>
      <a href="<?php echo $this->createUrl('/Ads/ad/viewDetails',$urlParams); ?>">
        <?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','here')?>
      </a>
      <?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','to view details of your ad.')?>
    </p>
<?php else : ?>
    <p><?php echo $errorMsg; ?></p>
<?php endif; ?>
</div>