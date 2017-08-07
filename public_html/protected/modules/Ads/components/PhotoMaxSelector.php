<?php
class PhotoMaxSelector extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $arr = array();
        for($i=1;$i<=5;$i++)
            $arr[$i] = $i;
        echo Language::t(Yii::app()->language,'Backend.Ads.Setting','up to ').CHtml::dropDownList($this->name,$this->value,$arr);    
    }
}