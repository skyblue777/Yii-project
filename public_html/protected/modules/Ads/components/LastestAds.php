<?php
class LastestAds extends FWidget
{
    public $limit = 3;
    public $selectedLocation = '';
    
    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->select = array('id','title','price','opt_price','description','area','photos','create_time');
        $criteria->addCondition("public = 1");
        if ($this->selectedLocation!='')
            $criteria->addCondition("area = '{$this->selectedLocation}'");    
        $criteria->order = "create_time DESC";
        $criteria->limit = intval($this->limit);
        $lastestAds = Annonce::model()->findAll($criteria);
        $this->render('LastestAds',
                      array('lastestAds'=>$lastestAds));    
    }
}