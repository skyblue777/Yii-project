<?php
class PDTIdentityTokenTextBox extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        echo CHtml::textField($this->name,$this->value,array('style'=>'width: 350px;')).' <a target="_blank" href="https://www.paypaltech.com/PDTGen/PDTtokenhelp.htm">'.Language::t(Yii::app()->language,'Backend.Money.Setting','Learn more').'</a>';    
    }
}