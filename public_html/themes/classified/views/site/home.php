<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
?>

<?php $this->widget('HomePageAdsGallery',array('selectedLocation'=>$this->get('location',''))); ?>
<div class="main-content">
    <?php $this->widget('AdsCategoryList',array('selectedLocation'=>$this->get('location',''))); ?>
    <div class="facebook">
        <fb:like href="<?php echo baseUrl(); ?>" send="false" width="450" show_faces="false"></fb:like>
    </div>     
</div>
<div class="aside">
    <p class="title"><strong><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Home','New ads')?></strong></p>
    <div class="box-aside">
        <?php if (intval(MapSettings::DISPLAY_MAP_HOMEPAGE)==1) $this->widget('HomePageGoogleMap',array('selectedLocation'=>$this->get('location',''),'mapMarkerLimit'=>MapSettings::MAP_MARKER)); ?>
        <?php if(trim(AdsSettings::AREA_LIST)!='') $this->widget('HomePageLocationSelector',array('selectedLocation'=>$this->get('location',''))); ?>
        <?php $this->widget('Ads.components.LastestAds',array('selectedLocation'=>$this->get('location',''))); ?>
        <p class="view-all"><a href="<?php $arrAllNewAdsParam = array(); if($this->get('location','')!='') $arrAllNewAdsParam['location'] = $this->get('location',''); echo $this->createUrl('/Ads/ad/listByArea',$arrAllNewAdsParam); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Home','All new ads')?></a></p>
    </div>
</div> 