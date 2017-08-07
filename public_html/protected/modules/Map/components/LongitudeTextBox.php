<?php
class LongitudeTextBox extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        echo CHtml::textField($this->name,$this->value).' <a target="_blank" href="http://itouchmap.com/latlong.html">'.Language::t(Yii::app()->language,'Backend.Map.Setting','Find out Latitude / Longitude').'</a>';    
    }
}