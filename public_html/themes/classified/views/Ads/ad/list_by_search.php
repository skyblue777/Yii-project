<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');

$bcrumbs = array();
if ($cat_id  != 0) {
  $cat = Category::model()->findByPk($cat_id);
  if (!is_null($cat) && $cat->parent_id != 0)
  {
    $catParams = array('/Ads/ad/listByCategory',
                       'cat_id'=>$cat->id,
                       'alias'=>$cat->alias,);
    if ($area != '')
      $catParams['location'] = $area;
    $bcrumbs[$cat->title] = $catParams;
  }
}
if ($keyword!='')
    $bcrumbs[] = $keyword;
if (!empty($bcrumbs)) {
  $this->breadcrumbs = $bcrumbs;
}
?>

<style type="text/css">
#ads-grid { width: 980px; margin: 0 auto; }
</style>

<?php
if (!is_null($dataProvider)) :
$currentPage = $this->get('Annonce_page', 1);
//print_r($currentPage);die;
//print_r($cat_id);

$this->widget('AdsListSummary',
  array('text'=>$keyword,
    'dataProvider'=>$dataProvider,
    'currentPage'=>$currentPage-1,
));

if ($dataProvider->totalItemCount>0)
{
  echo '<div class="sortby">';
  echo '<label id="listed">'.Language::t(Yii::app()->language,'Frontend.Ads.List','Sort by').'</label>';
  
  $sortParams = array('cat_id'=>$cat_id,
                      'alias'=>$alias,
                      'keyword'=>$keyword,
                      'isSort'=>true,
                      'Annonce_page'=>$currentPage,);
  if ($area != '')
    $sortParams['location'] = $area;
  $selected = $this->get('Annonce_sort', '');
  $html = CHtml::dropDownList(
    'listed', $selected,
    array(
       'create_time.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','New listed'),
        'price'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Lowest price first'),
        'price.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Highest price first'),
    ),
    array(
      'onchange'=>'window.location.href="'
      .Yii::app()->createUrl('Ads/ad/listBySearch',$sortParams)
      .'&Annonce_sort="+$("select#listed").val()',
    )
  );
  echo $html.'</div>';
}

$grid = $this->widget('Ads.extensions.AdsGridView', array(
    'id'=>'ads-grid',
    'hideHeader'=>true,
    'ajaxUpdate'=>false,
    'itemsCssClass'=>'list-category',
    'htmlOptions'=>array('class'=>''),
    'pager'=>array('class'=>'AdsLinkPager'),
    'template'=>"{items}\n{pager}",
    'dataProvider'=>$dataProvider,
    'columns'=>array(
        array(
            'filter'=>false,
            'type'=>'raw',
            'value'=>'$data->getImageSection("'.$area.'")',
            'htmlOptions'=>array('style'=>'width: 70px;padding-right; text-align: center;'),
        ),  
        array(
            'filter'=>false,
            'type'=>'raw',
            'value'=>'$data->getTitleContentSection("'.$area.'")',
            'htmlOptions'=>array('style'=>'width: 660px;'),
        ),
        array(
            'type'=>'raw',
            'filter'=>false,
            'value'=>'$data->getPriceSection()',
            'htmlOptions'=>array('style'=>'width: 230px;'),
        ),
    ),
));

endif;
?>
