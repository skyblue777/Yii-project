<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::import('zii.widgets.grid.CGridView');
Yii::import('Ads.extensions.AdsLinkPager');

class AdsGridView extends CGridView
{
	/**
	 * @var boolean whether to enable render Top Ads list. When Top Ads is enabled,
	 * a Top Ads list will be displayed in the view on top of the Ads list.
	 * Defaults to false.
	 */
	public $enableTopAds=false;
  
  /**
	 * @var boolean whether to regconize it is Top Ads Gridview or not.
	 * Defaults to false.
	 */
	public $isTopAds=false;
  
  /**
	 * @var IDataProvider the data provider for the top Ads list.
	 */
	public $dataTopAdsProvider;
  		  
  /**
	 * Renders the data items for the grid view.
	 */
	public function renderItems()
	{
		if($this->dataProvider->totalItemCount>0)
		{
      if ($this->isTopAds)
      {
        echo '<div class="'.$this->itemsCssClass.'">';
        $this->renderTopAdsHeader();
        $this->renderTopAdsBody();
        $this->renderTopAdsFooter();
        echo '</div>';
      } else
      {
        echo '<table class="'.$this->itemsCssClass.'" cellspacing="0" cellspadding="0">';
        $this->renderTableHeader();
        $this->renderTableBody();
        $this->renderTableFooter();
        echo '</table>';
      }
		}
	}
   
  /**
   * Renders the Top Ads list
   */
  public function renderTopAds() 
  {
    if(($this->dataProvider->totalItemCount>0) &&
       $this->enableTopAds &&
       ($this->dataTopAdsProvider->totalItemCount>0))
		{
      $grid = $this->widget('AdsGridView', array(
          'id'=>'top-ads-grid',
          'itemsCssClass'=>$this->itemsCssClass,
          'htmlOptions'=>$this->htmlOptions,
          'template'=>"{items}",
//          'enablePagination'=>false,
//          'enableSorting'=>false,
          'isTopAds'=>true,
          'dataProvider'=>$this->dataTopAdsProvider,
          'columns'=>array(
              array(
                  'filter'=>false,
                  'type'=>'raw',
                  'value'=>'$data->getImageSection()',
              ),  
              array(
                  'filter'=>false,
                  'type'=>'raw',
                  'value'=>'$data->getTitleContentSection()',
              ),
              array(
                  'type'=>'raw',
                  'filter'=>false,
                  'value'=>'$data->getPriceSection()',
              ),
          ),
      ));
    }
  }
  
  /**
   * Renders the Top Ads header
   */
  public function renderTopAdsHeader()
  {
    echo '<div style="display:table">';
    echo '<div style="width:70px;" class="topads-header">Top ads</div>';
    echo '<div style="width:700px;" class="topads-header">&nbsp;</div>';
    echo '<div style="width:185px;" class="topads-header">'.Language::t(Yii::app()->language,'Frontend.Ads.List','Want to promote').' <a href="';
    echo Yii::app()->createUrl('site/faqs',array('alias'=>'what-are-top-ads'));
    echo '">'.Language::t(Yii::app()->language,'Frontend.Ads.List','your Ad here').'</a>?</div>';
    echo '</div>';
  }
  
  /**
   * Renders the Top Ads body
   */
  public function renderTopAdsBody()
  { 
    $totalTopAdsItems = $this->dataProvider->totalItemCount;
    
    $data=$this->dataProvider->getData();
    $n=count($data);
    
    if ($totalTopAdsItems > AdsSettings::MAX_FAD) {
      Yii::app()->clientScript->registerScriptFile(themeUrl().
          '/scripts/jquery.jcarousel.min.js',CClientScript::POS_HEAD);
    }
    
    echo '<ul id="top-ads">';

    if($n>0)
    {
        for($row=0;$row<$n;++$row)
            $this->renderTopAdsRow($row);
    }
    echo '</ul>';
    
    if ($totalTopAdsItems > AdsSettings::MAX_FAD) {
      echo '<script type="text/javascript">';
      echo "$(document).ready(function() {\n\t";
      echo "$('#top-ads').jcarousel({
                  auto: 5,
                  visible: ".AdsSettings::MAX_FAD.",
                  scroll: 2,
                  animation: 'slow',
                  vertical: true,
                  wrap: 'circular'});";
      echo "});\n";
      echo '</script>';
    }
  }
  
  /**
   * Renders the Top Ads body
   */
  public function renderTopAdsRow($row)
  {
    echo '<li style="display:table;">';
    $i = 1;
    foreach($this->columns as $column)
    {
        $data=$this->dataProvider->data[$row];
        if ($i == 1)
            echo '<div class="cell col-'.$i.'" style="text-align: center;">';    
        else
            echo '<div class="cell col-'.$i.'">';
        if($column->value!==null)
          $value=$column->evaluateExpression($column->value,array('data'=>$data,'row'=>$row));
        else if($column->name!==null)
            $value=CHtml::value($data,$column->name);
        echo $value===null ? $this->nullDisplay : $this->getFormatter()->format($value,$column->type);
        echo '</div>';
        
        $i++;
    }
    echo "</li>\n";
  }
  
  /**
   * Renders the Top Ads footer
   */
  public function renderTopAdsFooter()
  {
    echo '<div class="topads-footer">&nbsp;</div>';
  }
}
?>
