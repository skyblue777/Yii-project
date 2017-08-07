<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */
class ParamForm extends FWidget
{
    /**
    * @var array SettingParam models
    */
    public $params;
    /**
    * @var array configuration for each param to render its input element
    */
    public $config;
    
    public $elements = array();
    
    public function init() {
        parent::init();
        // prepare the list of input elements
        foreach($this->params as $param) {
            $elm = new ParamFormElement($param);
            if (isset($this->config[$param->name])) {
                $pCfg = $this->config[$param->name];
                if (isset($pCfg['type']))
                    $elm->type = $pCfg['type'];
                if (isset($pCfg['rules']) && is_array($pCfg['rules'])){
                    $elm->param->rules = array();
                    foreach($pCfg['rules'] as $rule => $options)
                    $elm->param->rules[] = array_merge(array('value', $rule), $options); // $pCfg['rules'];
                }
                if (isset($pCfg['items']))
                    $elm->items = $pCfg['items'];
                if (isset($pCfg['htmlOptions']))
                    $elm->htmlOptions = $pCfg['htmlOptions'];
            }
            $this->elements[$param->name] = $elm;
        }

        // save new values
        if (Yii::app()->request->IsPostRequest){
            foreach($this->params as &$param) {
                if (!isset($_POST[$param->name])) continue;
                
                $param->value = $_POST[$param->name];
                if (! $param->save()) {
                    FErrorHandler::logError($param->getError('value'));
                }
            }
            //update Settings Class
            if (isset($_POST['SettingParam'], $_POST['SettingParam']['module'])) {
                $modules = $_POST['SettingParam']['module'];
                if (empty($modules)) {
                    //render all
                    Yii::import('Core.models.SettingParam');
                    $modules = SettingParam::model()->getModules();
                    //remove 'System' module
                    if (count($modules))
                        array_pop($modules);
                } elseif ($modules == 'system_module') {
                    //update module system
                    $modules = array('');
                } else
                    $modules = array($modules);
                foreach ($modules as $module)
                    FSM::run('Core.Settings.db2php', array('module' => $module));
               //Refresh page if setting language
               if (isset($_POST['LANG'])) Yii::app()->request->redirect(Yii::app()->createUrl('Language/language/general'));
            }
        }
        
    }
    
    /**
    * Render param input field
    * 
    * @param string $name parameter name
    */
    public function renderParam($name){
        echo $this->elements[$name]->render();
    }
    
    public function beginForm() {
        echo "<div class=\"form wide\">\n<form action=\"\" method=\"post\" enctype=\"multipart/form-data\">";
    }
    
    public function endForm() {
        echo '<div class="row">',CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Common.Common','Save'),array('class' => 'buttons')),'</div>';
        echo "</form>\n</div>";
    }
    
    /**
    * render parameter form. Customized render script can use 
    * renderParam(), beginForm(), endForm() to show the form 
    */
    public function render(){
        $this->beginForm();
        
        foreach($this->elements as $name => $elm)
            $this->renderParam($name);
            
        $this->endForm();
    }
    
}

class ParamFormElement extends CComponent {
    /**
     * @var array Core input types (alias=>CHtml method name)
     */
    public static $coreTypes=array(
        'text'=>'textField',
        'hidden'=>'hiddenField',
        'password'=>'passwordField',
        'textarea'=>'textArea',
        'file'=>'fileField',
        'radio'=>'radioButton',
        'checkbox'=>'checkBox',
        'listbox'=>'listBox',
        'dropdownlist'=>'dropDownList',
        'checkboxlist'=>'checkBoxList',
        'radiolist'=>'radioButtonList',
    );

    public $param;
    
    public $type='text';
    
    public $items=array();
    
    public $htmlOptions=array();
    
    protected $layout="<div class=\"row\">\n{label}\n{input}\n{hint}\n{error}\n</div>";
    
    /**
    * @param SettingParam $param
    * @return ParamFormElement
    */
    public function __construct($param) {
        $this->param = $param;
    }
    
    public function render()
    {
        if ($this->param->visible == false) return '';
        
        $output=array(
            '{label}'=>$this->renderLabel(),
            '{input}'=>$this->renderInput(),
            '{hint}'=>$this->renderHint(),
            '{error}'=>'' //$this->getParent()->showErrorSummary ? '' : $this->renderError(),
        );
        return strtr($this->layout,$output);
    }
    
    /**
     * Renders the label for this input.
     * The default implementation returns the result of {@link CHtml activeLabelEx}.
     * @return string the rendering result
     */
    public function renderLabel()
    {
    	$label=Language::t(Yii::app()->language,'Backend.Admin.Setting',$this->param->label);
        return CHtml::label($label, $this->param->name);
    }

    /**
     * Renders the input field.
     * The default implementation returns the result of the appropriate CHtml method or the widget.
     * @return string the rendering result
     */
    public function renderInput()
    {
        if(isset(self::$coreTypes[$this->type]))
        {
            $method=self::$coreTypes[$this->type];
//            $value = Language::t(Yii::app()->language,'Backend.Admin.Setting-Value',$this->param->name);
//            if($value == $this->param->name)
//            	$value=$this->param->value;
            $value=$this->param->value;
            if(strpos($method,'List')!==false)
                return CHtml::$method($this->param->name, $value, $this->items, $this->htmlOptions);
            else
                return CHtml::$method($this->param->name, $value, $this->htmlOptions);
        }
        else
        {
            $attributes=$this->param->attributes;
            $attributes['model']=get_class($this->param);
            $attributes['attribute']=$this->param->name;
//            $attributes=$this->attributes;
//            $attributes['model']=$this->getParent()->getModel();
//            $attributes['attribute']=$this->name;
            ob_start();
            Yii::app()->controller->widget($this->type, $attributes);
            return ob_get_clean();
        }
    }

    /**
     * Renders the error display of this input.
     * The default implementation returns the result of {@link CHtml::error}
     * @return string the rendering result
     */
    public function renderError()
    {
    }

    /**
     * Renders the hint text for this input.
     * The default implementation returns the {@link hint} property enclosed in a paragraph HTML tag.
     * @return string the rendering result.
     */
    public function renderHint()
    {          
        return $this->param->description==''? '' : '<div class="hint">'.$this->param->description.'</div>';
    }
}
?>