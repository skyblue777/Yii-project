<?php
class BackgroundColorSelector extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $colors = array('blue-back','grey-back');
        if (empty($this->value)) $this->value = $colors[0];
        $this->render('BackgroundColorSelector',array('colors'=>$colors));    
    }
}