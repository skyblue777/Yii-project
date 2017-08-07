<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AdsLinkPager extends CBasePager
{
	public $nextPageLabel;
	/**
	 * @var string the text label for the previous page button. Defaults to '&lt; Previous'.
	 */
	public $prevPageLabel;
	
	/**
	 * @var string the CSS class name for the container of buttons display. Defaults to 'btn'.
	 */
	public $buttonCssClass='btn';
  
  /**
   * @var interger the start order numbers of Ads items that is displayed in the current page
   */
  private $start =1;
  
  /**
   * @var interger the end order numbers of Ads items that is displayed in the current page
   */
  private $end =1;
  
  /**
	 * Initializes the pager by setting some default property values.
	 */
	public function init()
	{
		if($this->nextPageLabel===null)
			$this->nextPageLabel=Language::t(Yii::app()->language,'Frontend.Common.Common','Next');
		if($this->prevPageLabel===null)
			$this->prevPageLabel=Language::t(Yii::app()->language,'Frontend.Common.Common','Previous');
	}

	/**
	 * Executes the widget.
	 * This overrides the parent implementation by displaying the generated page buttons.
	 */
	public function run()
	{
		if(($pageCount=$this->getPageCount())<=1)
			return;
    
    $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
    
    // prev page
		if(($page=$currentPage-1)<0)
			$page=0;
		echo $this->createPageButton($this->prevPageLabel,$page,$this->buttonCssClass,$currentPage<=0);
    
    // summary text
    $this->calculateStartEnd();
    echo '<span>'.$this->start.' - '.$this->end.' '.Language::t(Yii::app()->language,'Frontend.Common.Common','of').' '.$this->itemCount.'</span>';
    
    // next page
		if(($page=$currentPage+1)>=$pageCount-1)
			$page=$pageCount-1;
		echo $this->createPageButton($this->nextPageLabel,$page,$this->buttonCssClass,$currentPage>=$pageCount-1);
	}

	/**
	 * Creates a page button.
	 * You may override this method to customize the page buttons.
	 * @param string $label the text label for the button
	 * @param integer $page the page number
	 * @param string $class the CSS class for the page button. This could be 'next' or 'previous'.
	 * @param boolean $hidden whether this page button is visible
	 * @return string the generated button
	 */
	protected function createPageButton($label,$page,$class,$hidden)
	{
		if($hidden)
			$class.=' hidden';
		return '<a class="'.$class.'" href="'.$this->createPageUrl($page).'">'.$label.'</a>';
	}
  
  /**
   * Calculate the end order number & start order number
   */
  private function calculateStartEnd()
  {
    $this->start = ($this->currentPage)*$this->pageSize;
    $this->end = ($this->currentPage)*$this->pageSize;
    //print_r($end.' & '.$start);die;
    if ($this->itemCount > 0) {
      $this->start += 1;
      if (($this->pageSize == 0) || ($this->pageSize > $this->itemCount)) {
        $this->end += $this->itemCount;
        //
      } else { 
        //print_r($totalPages);die;
        if ($this->currentPage+1 < $this->pageCount) {
          $this->end += $this->pageSize;
        } else {
          $mod = $this->itemCount % $this->pageSize;
          if ($mod == 0) {
            $this->end += $this->pageSize;
          } else {
            $this->end += $mod;
          }
        }
      }
    }
  }
}
?>