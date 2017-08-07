<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');

if (!is_null($cat))
{
    $bcrumbs = array();
    $bcrumbs[] = $cat->title;

    $cat_ = $cat;
    while (!is_null($cat_) && ($cat_->parent_id!=AdsSettings::ADS_ROOT_CATEGORY))
    {
        $cat_ = Category::model()->findByPk($cat_->parent_id);
        if (!is_null($cat_))
        {
            $catParams = array('/Ads/ad/listByCategory',
                         'cat_id'=>$cat_->id,
                         'alias'=>$cat_->alias,);
            if ($area != '') $catParams['location'] = $area;
            $bcrumbs[$cat_->title] = $catParams;
        }
        else break 1;
    }
    if (!is_null($bcrumbs))
        $this->breadcrumbs = array_reverse($bcrumbs, true);
}
?>

<style type="text/css">
.warning-message p, .warning-message h2 { margin-top: 10px; }
</style>

<div class="warning-message">
    <h2><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Warning & disclaimer')?></h2>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message1')?></p>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message2')?></p>
    <ul class="warning-conditions">
        <li><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message3')?></li>
        <li><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message4')?></li>
        <li><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message5')?></li>
        <li><?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Message6')?></li>
    </ul>
</div>

<div style="text-align: center;">
    <form method="post">
        <input type="submit" class="btn" value="<?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Enter')?>" name="acceptWarningPage"  />
        <input type="button" class="btn" value="<?php echo Language::t(Yii::app()->language,'Frontend.Ads.WarningPage','Go back')?>" onclick="javascript:history.go(-1);" />
    </form>
</div>