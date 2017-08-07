<?php
class AdsCategoryList extends FWidget
{
    protected $topCats = array();
    public $selectedLocation = '';
    public $type = 'for_listing';
    //public $ad_id = '';
    
    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->select = array('id','title','image','alias','show_ad_counter');
        $criteria->condition = "parent_id = ".AdsSettings::ADS_ROOT_CATEGORY;
        $criteria->order = "ordering ASC";
        $this->topCats = Category::model()->findAll($criteria);
        $this->render('AdsCategoryList');    
    }
    
    public function generateCategoriesByColumn($startIndex)
    {
        $str = '';
        if (isset($this->topCats[$startIndex]))
        {
            for($i=$startIndex;$i<count($this->topCats);$i=$i+3)
            {
                $criteria = new CDbCriteria();
                $criteria->select = array('id','title','alias','paid_ad_required');
                $criteria->condition = "parent_id = {$this->topCats[$i]->id}";
                $criteria->order = "ordering ASC";
                $subCats = Category::model()->findAll($criteria);
                $subCatIds = array();
                $strSubCats = '';
                foreach($subCats as $subCat)
                {
                    if ($this->topCats[$i]->show_ad_counter == 1) $subCatIds[] = $subCat->id;
                    $urlParams = array('cat_id'=>$subCat->id, 'alias'=>$subCat->alias);
                    if ($this->selectedLocation != '')
                        $urlParams['location'] = $this->selectedLocation;
                    if ($this->type=='for_listing')
                    {
                        $strSubCats .= '<li><a href="'.Yii::app()->createUrl('Ads/ad/listByCategory',$urlParams).'">'
                                       .$subCat->title.'</a></li>';
                    }
                    else
                    {
//                        if ($this->ad_id != '')
//                        {
//                          $isPaid = false;
//                          $urlParams['id'] = $this->ad_id;
//                          $ad = Annonce::model()->findByPk($this->ad_id);
//                          if (isset($ad))
//                          {
//                            $cat = Category::model()->findByPk($ad->category_id);
//                            if (isset($cat) && $cat->paid_ad_required == 1)
//                              $isPaid = true;
//                          }
//                          
//                          if (!$isPaid && ($subCat->paid_ad_required == 1) && (MoneySettings::PAID_ADS_PRICE > 0))
//                            $strSubCats .= '<li><a href="'.Yii::app()->createUrl('Ads/ad/requirePaymentForPaidCategory',$urlParams).'">'
//                                           .$subCat->title.'</a></li>';
//                          else
//                            $strSubCats .= '<li><a href="'.Yii::app()->createUrl('Ads/ad/update',$urlParams).'">'.$subCat->title.'</a></li>';
//                        } else 
                        //{
                        if (($subCat->paid_ad_required == 1) && (MoneySettings::PAID_ADS_PRICE > 0))
                        {
                          $strSubCats .= '<li><a href="'.Yii::app()->createUrl('Ads/ad/requirePaymentForPaidCategory',$urlParams).'">'
                                         .$subCat->title.'</a></li>';
                        }
                        else
                          $strSubCats .= '<li><a href="'.Yii::app()->createUrl('Ads/ad/create',$urlParams).'">'
                                         .$subCat->title.'</a></li>';
                        //}
                    }
                }
                // count ads in all sub cats if this top cat is allow to show ad counter
                $countAds = 0;
                if ($this->topCats[$i]->show_ad_counter == 1)
                {
                    Yii::import('Ads.models.Annonce');
                    $cri = new CDbCriteria();
                    $cri->addInCondition('category_id',$subCatIds);
                    $cri->addCondition('public = 1');
                    if ($this->selectedLocation!='')
                        $cri->addCondition("area = '{$this->selectedLocation}'");
                    $countAds = Annonce::model()->count($cri);
                }
                $urlParams = array('cat_id'=>$this->topCats[$i]->id);
                if ($this->selectedLocation!='')
                    $urlParams['location'] = $this->selectedLocation;
                if ($this->type=='for_listing')
                {
                    $urlParams['alias'] = $this->topCats[$i]->alias;
                    $str .= '<p class="title">';
                    if($this->topCats[$i]->image){
                        $str .='<img src="'.Yii::app()->request->getBaseUrl(TRUE).'/uploads/category/'.$this->topCats[$i]->image.'" style="position:relative;left:-10px;top:10px;">';
                    }
                    $str .= '<a href="'.Yii::app()->createUrl('Ads/ad/listByCategory',$urlParams).'">';
                    $str .= '<strong>'.$this->topCats[$i]->title.'</strong></a>';
                }
                else
                    $str .= '<p class="title"><strong>'.$this->topCats[$i]->title.'</strong>';    
                if ($this->type=='for_listing' && $this->topCats[$i]->show_ad_counter == 1)
                    $str .= '<span style="font-size: 12px;"> ('.$countAds.')</span>';
                $str .= '</p>';
                $str .= '<ul class="categories">'.$strSubCats.'</ul>';
            }    
        }
        return $str;
    }
}