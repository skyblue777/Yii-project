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
 * @copyright Copyright &copy; 2009-2011 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */
class HintTextField extends CInputWidget {
    /**
    * The hint text that display by default inside the textbox
    * 
    * @var mixed
    */
    public $hint;
    
    public function run() {
        $method = 'textField';
        if (isset($this->htmlOptions['type']))
            if ($this->htmlOptions['type'] == 'password')
                $method = 'passwordField';
            elseif ($this->htmlOptions['type'] == 'textarea')
                $method = 'textArea';
        
        
        if (!empty($this->model) && !empty($this->attribute))
        {
            $method = 'active'.ucfirst($method);
            if (empty($this->model->{$this->attribute}))
                $this->model->{$this->attribute} = $this->hint;
            echo CHtml::$method($this->model, $this->attribute, $this->htmlOptions);
        }
        else
            echo CHtml::$method($this->name, $this->hint, $this->htmlOptions);
            
        $id = end($this->resolveNameID());
        $script = '
            $("#'.$id.'").focus(function(){
                if ($(this).val() == "'.$this->hint.'")
                    $(this).val("");
            });
            $("#'.$id.'").blur(function(){
                if ($(this).val() == "")
                    $(this).val("'.$this->hint.'");
            });
            $("#'.$id.'").parents("form").submit(function(){
                if ($("#'.$id.'").val() == "'.$this->hint.'")
                    $("#'.$id.'").val("");
            })
        ';
        Yii::app()->clientScript->registerScript(__CLASS__.$id, $script, CClientScript::POS_READY);
    }
}