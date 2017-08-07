<?php
class HomePageAdsGallery extends FWidget
{
    public $selectedLocation = '';
    
    public function run()
    {
        Yii::import('Ads.models.Annonce');
        $criteria = new CDbCriteria();
        $criteria->select = array('id','title','price','opt_price','area','photos');
        $criteria->condition = "homepage = 1 AND public = 1";
        if ($this->selectedLocation!='')
            $criteria->addCondition("area = '{$this->selectedLocation}'");
        $criteria->order = "create_time DESC";
        $ads = Annonce::model()->findAll($criteria);
        $this->render('HomePageAdsGallery',array('ads'=>$ads));    
    }
}