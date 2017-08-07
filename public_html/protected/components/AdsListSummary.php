<?php
class AdsListSummary extends FWidget
{
    public $text = '';
    public $dataProvider = null;
    public $currentPage = 0;

    public function run()
    {
        $pageSize = AdsSettings::MAX_RESULTS;
//      $pagination=$this->dataProvider->pagination;      
//      $currentPage = $pagination->currentPage;
        //print_r($pagination->currentPage);die;
        $count = $this->dataProvider->itemCount;
        //print_r($count);die;
        $total = $this->dataProvider->totalItemCount;
      
        $start = $this->currentPage*$pageSize+1;
        $end = $start+$count-1;
        if($end>$total)
        {
            $end = $total;
            $start = $end-$count+1;
        }
      
        $summaryText = '';
      
        if ($total == 0)
        {
            if ($this->text!='')
                $summaryText = Language::t(Yii::app()->language,"Frontend.Common.Common","Results for").' '.$this->text.'. '.Language::t(Yii::app()->language,"Frontend.Common.Message",'Sorry, there is no result');
            else
                $summaryText = Language::t(Yii::app()->language,"Frontend.Common.Common","Results").' : '.Language::t(Yii::app()->language,"Frontend.Common.Message",'Sorry, there is no result');    
        }
        else
        {
            if ($this->text!='')
                $summaryText = Language::t(Yii::app()->language,"Frontend.Common.Common","Results for").' '.$this->text.' '.$start.' - '.$end.' '.Language::t(Yii::app()->language,"Frontend.Common.Common","of").' '.$total;
            else
                $summaryText = Language::t(Yii::app()->language,"Frontend.Common.Common","Results").' : '.$start.' - '.$end.' '.Language::t(Yii::app()->language,"Frontend.Common.Common","of").' '.$total;
//          $summaryText = 'Results for {text} {start} - {end} of {count}';
//          strtr($summaryText,
//          array(
//              '{text}'=>$this->text,
//              '{start}'=>$start,
//              '{end}'=>$end,
//              '{count}'=>$total,
//        ));        
        }
      
        $this->render('AdsListSummary', array('summaryText'=>$summaryText));    
    }
}