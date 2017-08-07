<?php
class CurrencyDropDownList extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $currencies = Lookup::model()->items('currency');
        echo CHtml::dropDownList($this->name,$this->value,$currencies);    
    }
}