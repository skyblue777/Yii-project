<?php
class HomePageLocationSelector extends FWidget
{
    public $selectedLocation = '';
    
    public function run()
    {
        $areas = explode(';',AdsSettings::AREA_LIST);
        $locations = array();
        foreach($areas as $key => $area)
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
        $this->render('HomePageLocationSelector',array('locations'=>$locations));    
    }
}