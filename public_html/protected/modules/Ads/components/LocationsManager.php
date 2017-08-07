<?php
class LocationsManager extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $locations = array();
        $strLocations = trim($this->value);
        if ($strLocations != '')
        {
            $areas = explode(';',$strLocations);
            foreach($areas as $area)
            {
                $area = trim($area);
                if (!empty($area))
                {
                    $arrAreaParts = explode('|',$area);
                    if (count($arrAreaParts)!=2) continue;
                    $latLng = explode(',',trim($arrAreaParts[1]));
                    if (count($latLng)!=2) continue;
                    $locations[] = array(
                        'name' => trim($arrAreaParts[0]),
                        'lat' => $latLng[0],
                        'lng' => $latLng[1]
                    );
                }    
            }    
        }
        $this->render('LocationsManager',array('locations'=>$locations));   
    }
}