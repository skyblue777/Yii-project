<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.Ads.Common','Update your ad')
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Ads.Common','Update your ad');
?>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Common','Update your ad')?></h1>

<div class="form create-ads">
    <?php if (!is_null($model->category)) echo $this->renderPartial('_form', array('model'=>$model,'cat'=>$model->category)); ?>
</div>