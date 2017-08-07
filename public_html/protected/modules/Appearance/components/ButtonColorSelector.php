<?php
class ButtonColorSelector extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $colors = array('green','orange','blue','yellow','red','pink','purple');
        if (empty($this->value)) $this->value = $colors[0];
        $this->render('ButtonColorSelector',array('colors'=>$colors));    
    }
}