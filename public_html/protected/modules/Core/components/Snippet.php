<?php
class Snippet extends FWidget
{
    public $name;
    
    private $_viewData = array();
    
    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch(Exception $ex) {
            $this->_viewData[$name] = $value;
        }
    }
    
    public function run(){
        $this->render($this->name, $this->_viewData);
    }
}
?>