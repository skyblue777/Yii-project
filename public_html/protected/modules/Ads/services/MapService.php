<?php
class MapService extends FServiceBase
{    
    public function getLatLngByLocation($params)
    {
        $location = trim($this->getParam($params,'location',''));
        $lat = '0';
        $lng = '0';
        
        $locations = explode(';',AdsSettings::AREA_LIST);
        foreach($locations as $area)
        {
            $area = trim($area);
            if (empty($area)) continue;
            $arrAreaParts = explode('|',$area);
            if (count($arrAreaParts)!=2) continue;
            if ($location == trim($arrAreaParts[0]))
            {
                $arrLatLng = explode(',',$arrAreaParts[1]);
                $lat = trim($arrLatLng[0]);
                $lng = trim($arrLatLng[1]);
                break;
            }
        }
        
        $this->result->processed('lat',$lat);
        $this->result->processed('lng',$lng);
        
        return $this->result;
    }   
}
