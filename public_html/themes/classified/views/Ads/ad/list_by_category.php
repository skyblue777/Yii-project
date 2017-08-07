<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');

if (!is_null($cat))
{
  $bcrumbs = array();
  $bcrumbs[] = $cat->title;

  $cat_ = $cat;
  while (!is_null($cat_) && ($cat_->parent_id!=AdsSettings::ADS_ROOT_CATEGORY)) {
    $cat_ = Category::model()->findByPk($cat_->parent_id);
    if (!is_null($cat_)){
      $catParams = array('/Ads/ad/listByCategory',
                         'cat_id'=>$cat_->id,
                         'alias'=>$cat_->alias,);
      if ($area != '')
        $catParams['location'] = $area;
      $bcrumbs[$cat_->title] = $catParams;
    } else {
      break 1;
    }
  }
  if (!is_null($bcrumbs)) {
    $this->breadcrumbs = array_reverse($bcrumbs, true);
  }
}
?>

<style type="text/css">
#ads-grid { width: 980px; margin: 0 auto; }
.list-category .topads-header {
  background-color:#eeeeee;
  border-top:1px solid #989898;
  padding:5px 5px 5px 10px;
  font-size:12px;
  font-weight:bolder;
  text-align:left;
  display:table-cell;
}
.list-category .topads-footer {
  background-color:#eeeeee;
  border-bottom:1px solid #989898;
  border-top:1px solid #e6e5ea;
  padding:5px 5px 5px 10px;
  font-size:0;
  height:13px;
  padding:0;
}
.jcarousel-container .jcarousel-clip-vertical
{
  height:500px;
}
</style>

<?php if (!is_null($cat) &&
          !is_null($dataTopAdsProvider) &&
          !is_null($dataProvider)) :
  $currentPage = $this->get('Annonce_page', 1);
  $text = $cat->title;
  if ($area != '')
    $text .= '('.$area.')';
  $this->widget('AdsListSummary',
    array('text'=>$text,
      'dataProvider'=>$dataProvider,
      'currentPage'=>$currentPage-1,
  ));
  if ($dataProvider->totalItemCount>0)
  {
    echo '<div class="sortby">';
    echo '<label id="listed">'.Language::t(Yii::app()->language,'Frontend.Ads.List','Sort by').'</label>';
    
    $sortParams = array('cat_id'=>$cat->id,
                        'alias'=>$cat->alias,
                        'Annonce_page'=>$currentPage,);
    if ($area != '')
      $sortParams['location'] = $area;
    $selected = $this->get('Annonce_sort', '');
    if ($selected == '') {
      if (AdsSettings::RESULT_SORT == 1)
        $selected = 'create_time.desc';
      else
        $selected = 'price';
    }
    $html = CHtml::dropDownList(
      'listed', $selected,
     array(
        'create_time.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','New listed'),
        'price'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Lowest price first'),
        'price.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Highest price first'),
      ),
      array(
        'onchange'=>'window.location.href="'
        .Yii::app()->createUrl('Ads/ad/listByCategory',$sortParams)
        .'&Annonce_sort="+$("select#listed").val()',
      )
    );
    echo $html.'</div>';
  }
  $grid = $this->widget('Ads.extensions.AdsGridView',
    array(
      'id'=>'ads-grid',
      'hideHeader'=>true,
      'ajaxUpdate'=>false,
//      'emptyText'=>'Results for '.$cat->title.' â€œSorry, there is no resultâ€�.',
      //'summaryText'=>Language::t(Yii::app()->language,"Frontend.Common.Common","Results").' {start} - {end} '.Language::t(Yii::app()->language,"Frontend.Common.Common","of").' {count}',
//      'summaryCssClass'=>'title-2',
      'itemsCssClass'=>'list-category',
      'htmlOptions'=>array('class'=>''),
      'pager'=>array('class'=>'AdsLinkPager'),
      'template'=>"{topAds}\n{items}\n{pager}",
      'enableTopAds'=>true,
      'dataTopAdsProvider'=>$dataTopAdsProvider,
      'dataProvider'=>$dataProvider,
      'columns'=>array(
          array(
              'filter'=>false,
              'type'=>'raw',
              'value'=>'$data->getImageSection()',
              'htmlOptions'=>array('style'=>'width: 70px;padding-right; text-align: center;'),
          ),  
          array(
              'filter'=>false,
              'type'=>'raw',
              'value'=>'$data->getTitleContentSection()',
              'htmlOptions'=>array('style'=>'width: 700px;'),
          ),
          array(
//              'name'=>'price',
//              'header'=>"Price",
              'type'=>'raw',
              'filter'=>false,
              'value'=>'$data->getPriceSection()',
              'htmlOptions'=>array('style'=>'width: 190px;'),
          ),
      ),
  ));
  
  if (AdsSettings::RSS_FEED) {
    if ($dataProvider->totalItemCount > AdsSettings::MAX_RESULTS)
      echo '<div class="RSS">';
    else
      echo '<div style="margin-top:30px;" class="RSS">';
    echo Chtml::link('<img alt="Ads Feed" src="'.baseUrl().'/'.'images/RSS.png">',
                     Yii::app()->createUrl('Ads/ad/feed',
                                           array('cat_id'=>$cat->id,
                                                 'location'=>$area)));
    echo '</div>';
  };

else : ?>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This category does not exist.')?></p>
<?php endif; ?>
