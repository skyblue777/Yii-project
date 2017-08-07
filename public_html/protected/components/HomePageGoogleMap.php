<?php
class HomePageGoogleMap extends FWidget
{
    public $mapMarkerLimit = 20;
    public $selectedLocation = '';
    
    public function run()
    {
        Yii::import('Ads.models.Annonce');
        $criteria = new CDbCriteria();
        $criteria->select = array('id','title','lat','lng','price','opt_price','area');
        $criteria->addCondition("public = 1");
        $criteria->addCondition("lat <> '' AND lat <> 0 AND lng <> '' AND lng <> 0");
        if ($this->selectedLocation!='')
            $criteria->addCondition("area = '{$this->selectedLocation}'");
        $criteria->order = "create_time DESC";
        $criteria->limit = intval($this->mapMarkerLimit);
        $ads = Annonce::model()->findAll($criteria);
        // get lat, lng
        $lat = MapSettings::LATITUDE;
        $lng = MapSettings::LONGITUDE;
        $result = FSM::run('Ads.Map.getLatLngByLocation',array('location'=>$this->selectedLocation));
        if ($result->lat!='0' && $result->lng!='0')
        {
            $lat = $result->lat;
            $lng = $result->lng;
        }
        $this->render('HomePageGoogleMap',
                      array('ads'=>$ads,
                            'lat'=>$lat,
                            'lng'=>$lng,));    
    }
}