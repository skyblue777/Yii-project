<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category') => array('/Ads/ad/selectCategory'),
    Language::t(Yii::app()->language,'Frontend.Ads.Common','Create your ad'),
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Ads.Common','Create your ad');
?>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Common','Create your ad')?></h1>

<div class="form create-ads">
    <?php if (is_null($model)) : ?>
        <div class="errorMessage" style="margin-left: 0px;"><?php echo $errorMsg; ?></div>
    <?php else :
        echo $this->renderPartial('_form', array('model'=>$model,'cat'=>$cat));
    endif;    
    ?>
</div>