<?php
class BannedWordsTextArea extends CInputWidget
{
    public $label;
    public $description;
    public $setting_group;
    public $ordering;
    public $visible;
    public $module;
        
    public function run()
    {
        $this->render('BannedWordsTextArea');    
    }
}