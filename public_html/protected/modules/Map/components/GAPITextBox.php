<?php
class GAPITextBox extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        echo CHtml::textField($this->name,$this->value,array('style'=>'width: 360px;')).' <a target="_blank" title="See the section \'Obtaining an API Key\'" href="http://code.google.com/apis/maps/documentation/javascript/tutorial.html#api_key">'.Language::t(Yii::app()->language,'Backend.Map.Setting','Get a key').'</a>';    
    }
}