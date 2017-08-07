<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');


$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Common.Layout','Advanced search');

$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','Advanced search')
);

if ($this->get('mode', '') == '') : ?>

<style type="text/css">
.create-ads label { width: 150px; }
.create-ads .errorMessage { margin-left: 158px; }
.create-ads .buttons { padding-left: 150px; }
</style>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Advanced search')?></h1>
<div class="form adv-search">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'advanced-search-form',
        'method'=>'get',
        'action'=>array('/Ads/ad/advancedSearch'),
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit'=>true,
        ),
    )); ?>
        <input type="hidden" name="mode" value="search" />
        <div class="row">
            <label for="txt-keyword"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.AdvancedSearch','Search for')?></label>
            <?php echo $form->textField($model,'searchedKeyword',array('class'=>'text-1')); ?>
            <div class="check">
                <?php echo $form->checkBox($model,'exactPhrase'); ?><label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.AdvancedSearch','exact phrase')?></label>
            </div>
        </div>
        <div class="row">
            <label for="txt-adid"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Preview','Ad ID')?></label>
            <?php echo $form->textField($model,'id',array('class'=>'text-2')); ?>
        </div>
        <div class="row">
            <label for="cate"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Category')?></label>
            <?php
            echo $form->dropDownList($model,'category_id',
                        CHtml::listData(Category::model()->findAll(
                            new CDbCriteria(
                                array('condition'=>'parent_id='.AdsSettings::ADS_ROOT_CATEGORY,
                                  'order'=>'ordering ASC')
                                )),'id','title'),
                        array('prompt'=>Language::t(Yii::app()->language,'Backend.Common.Common','All'))
            );
            ?>
        </div>
        <div class="row">
            <label for="loca"><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Location')?></label>
            <?php
            $areas = explode(';',AdsSettings::AREA_LIST);
            $locations = array();
            foreach($areas as $area)
            {
                $area = trim($area);
                if (!empty($area))
                {
                    $arrAreaParts = explode('|',$area);
                    if (count($arrAreaParts)==2)
                        $area = trim($arrAreaParts[0]);
                    $locations[$area] = $area;
                }    
            }
            echo $form->dropDownList($model,'area',$locations,array('class'=>'select-1','prompt'=>Language::t(Yii::app()->language,'Backend.Common.Common','All')));
            ?>
        </div>
        <div class="row">
            <label><?php echo Language::t(Yii::app()->language,'Backend.Ads.Common','Zip code')?></label>
            <?php echo $form->textField($model,'zipcode',array('class'=>'text-3')); ?>
        </div>
        <div class="row">
            <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Price')?></label>
            <?php echo $form->textField($model,'priceFrom',array('class'=>'text-4')); ?>
            <label class="type"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','to')?></label>
            <?php echo $form->textField($model,'priceTo',array('class'=>'text-4')); ?>
        </div>
        <div class="row">
            <label><?php echo Language::t(Yii::app()->language,'Frontend.Ads.AdvancedSearch','With Photo')?></label>
            <?php echo $form->checkBox($model,'searchWithPhoto'); ?>
        </div>
        <div class="row buttons">
          <input class="btn" type="submit" value="<?php echo Language::t(Yii::app()->language,'Frontend.Ads.AdvancedSearch','Search')?>" style="margin-left: 0;" />
        </div>
    <?php $this->endWidget(); ?>
</div>

<?php else :

  if (!is_null($dataProvider)) :
  $currentPage = $this->get('Annonce_page', 1);
  $this->widget('AdsListSummary',
    array('text'=>$model->searchedKeyword,
      'dataProvider'=>$dataProvider,
      'currentPage'=>$currentPage-1,
  ));

  if ($dataProvider->totalItemCount>0)
  {
      $sortParams = array(
          'mode'=>'search',
          'Annonce_page'=>$currentPage,
          'isSort'=>true,
      );
      if (isset($_GET['Annonce']))
      {
          if (is_array($_GET['Annonce']))
          {
              $adParams = $_GET['Annonce'];
              foreach($adParams as $key => $param)
              {
                  $sortKey = 'Annonce['.$key.']';
                  $sortParams[$sortKey] = $param;        
              }
          }        
      }
      
      $selected = $this->get('Annonce_sort', '');
      echo '<div class="sortby">';
      echo '<label id="listed">'.Language::t(Yii::app()->language,'Frontend.Ads.List','Sort by').'</label>';
      $html = CHtml::dropDownList(
          'listed', $selected,
         array(
      		'create_time.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','New listed'),
        	'price'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Lowest price first'),
        	'price.desc'=>Language::t(Yii::app()->language,'Frontend.Ads.List','Highest price first'),
    		),
          array(
              'onchange'=>'window.location.href="'
                  .Yii::app()->createUrl(
                      'Ads/ad/advancedSearch',
                      $sortParams
              ).'&Annonce_sort="+$("select#listed").val()',
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
              'value'=>'$data->getImageSection()',
              'htmlOptions'=>array('style'=>'width: 70px;padding-right;'),
          ),  
          array(
              'filter'=>false,
              'type'=>'raw',
              'value'=>'$data->getTitleContentSection()',
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

endif;?>