<?php
class ExpireDayTextBox extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        echo CHtml::textField($this->name,$this->value).' '.Language::t(Yii::app()->language,'Backend.Language.Time','days');    
    }
}