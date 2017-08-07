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


class FHtml extends CHtml{
    /**
    * Prepare how error messages are displayed when jvalidate find errors
    * - Label of the error field will be highlighted with 'error' class
    * - Error message is displayed under the input box
    * Note that in order to place error message correctly, form's HTML must follow XHTML Strict rule.
    * The field label and field input control must be wrapped in a block tag, i.e. div
    * The error message is wrapped in label tag and use error class 'invalid'. Css for this
    * label should be {display:block; margin-left: ...px; color: red}
    */
    public static function hightlightErrorFields(){
        parent::setOptions(array(
                                'errorClass' => 'invalid',
                                'highlight' => 'function(element, errorClass) {
                                   $(element).addClass(errorClass);
                                   $(element.form).find("label[for=" + element.id + "]").addClass("error");
                                }',
                                'unhighlight' => 'function(element, errorClass) {
                                   $(element).removeClass(errorClass);
                                   $(element.form).find("label[for=" + element.id + "]").removeClass("error");
                                }',
                                'errorPlacement' => 'function(error, element) {
                                    $(element).parent().append(error);
                                }',
                                ));
    }
        
    /**
    * Generate submit button that does not submit data to form's default action
    * 
    * @param string $label
    * @param string $url
    * @param array $htmlOptions
    * @return string
    */
    public static function submitButton2nd($label, $url, $htmlOptions = array()) {
        $htmlOptions['submit'] = $url;
        return parent::submitButton($label, $htmlOptions);
    }

   /**
   * Show a list of error messages to user
   * 
   * @param mixed $errorMessages
   * @return mixed
   */
   public static function showErrors($errorMessages, $errorLeading = null){
       if (count($errorMessages) < 1) return '';
       if (!is_array($errorMessages)) $errorMessages = array($errorMessages);
       echo "<div class=\"ErrorSummary\">\n";
       if ($errorLeading === null) $errorLeading = "Please fix the following errors:";
       echo "<p>{$errorLeading}</p>";
       echo "<ul>\n";
       foreach ($errorMessages as $error){
           if (is_array($error)) $error = $error[0];
           echo "<li>{$error}</li>\n";
       }
       echo "</ul>\n</div>\n";
   }
    
    /**
    * Create a dropdown list for foreign key field
    * 
    * @param mixed $model
    * @param mixed $relation name of the relation which declare relationship between the "$model" and its parent
    * @param mixed $titleColumn name of the column used as title for parent object
    * @return string
    */
    public static function activeFkDropdown($model, $relation, $titleColumn, $criteria = null){
        //Get relationship info
        $relations = $model->relations();
        //create parent's AR object
        $class = $relations[$relation][1];
        $parent = new $class;
        //We have the field name of the FK field in the relationship info
        $attribute = $relations[$relation][2];
        //OK, enough info to create a dropdown list
        if ($criteria == null) $criteria = new CDbCriteria();
        
        return parent::activeDropDownList($model, $attribute, parent::listData($parent->model()->findAll($criteria), 'Id', $titleColumn));
    }
        
    /**
    * Render a TinyMCE editor
    * 
    * @param object $model
    * @param string $attribute
    * @param array $options
    */
    public static function activeRichEditor($model, $attribute, $options = array()){
        $widget = yii::createComponent('application.extensions.gui.tinymce.ETinyMce');
        $widget->model = $model;
        $widget->attribute = $attribute;
        
        if (count($options) == 0){
            $options =  array(  'width' => '515px',
                                'height' => '265px',
                                );
            $widget->EditorTemplate = 'custom';
        }
        $widget->setOptions($options);

        ob_start();
        $widget->init();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
    * DatePicker control
    * 
    * @param mixed $name
    * @param string $date
    * @param mixed $dateFormat
    * @return boolean
    */
    public static function datePicker($name, $date = null, $dateFormat = null, $calendarDateFormat = '%m/%d/%Y'){
        if ($dateFormat == null)
            $dateFormat = 'm/d/Y';
            
        if ($date != null)
            $date = date($dateFormat, strtotime($date));
        else
            $date = date($dateFormat);
        
        $html  = XHtml::textField($name, $date, array('class' => 'CalendarTextbox', 'readonly' => 'readonly', 'id' => "{$name}_textbox"));
        $html .= XHtml::image(Yii::app()->theme->BaseUrl."/images/ico-calendar.gif", 'calendar', array('id' => "{$name}_button", 'style' => 'cursor:pointer'));
        ob_start();
        Yii::app()->controller->widget('application.extensions.gui.calendar.SCalendar',
            array(
            'inputField' => "{$name}_textbox",
            'button' => "{$name}_button",
            'stylesheet' => Yii::app()->Params['calendarStyle'],
            'ifFormat' => $calendarDateFormat,
            ), 1);
        $html .= ob_get_clean();
            
        return $html;
    }
    /**
    * Render a TinyMCE editor
    *     
    * @param string $name
    * @param string $value
    * @param array $options
    */
    public static function richEditor($name, $value, $options = array()){
        $widget = yii::createComponent('application.extensions.gui.tinymce.ETinyMce');
        $widget->name = $name;
        $widget->value = $value;
        
        if (count($options) == 0){
            $widget->EditorTemplate = 'custom';
        }
        $widget->setOptions($options);
        //Set widget width and height
        if(isset($options['height']))
            $widget->Height = $options['height'];
        else
            $widget->Height = '250px';
        if(isset($options['width']))
            $widget->Width = $options['width'];
        else
            $widget->Width = '518px'; //min width that the toolbar fits in

        ob_start();
        $widget->init();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    /**
    * And HTML link to show/hide a related div
    * 
    * @param string $expandedCaption
    * @param string $collapsedCaption
    * @param string $containerId ID of the div to be toggled
    */
    public static function toggleLink($expandedCaption, $collapsedCaption, $containerId){
        $controlId = $containerId.'_handler';
        $html = "<a id=\"{$controlId}\" href=\"#\" title=\"\"></a>";
        $script = "
            \$('#{$controlId}').click(function(){
                \$('#{$containerId}').toggle();
                if (\$('#{$containerId}').css('display') != 'none')
                    \$('#{$controlId}').html('{$expandedCaption}');
                else
                    \$('#{$controlId}').html('{$collapsedCaption}');
                    
                return false;
            });
            \$('#{$controlId}').trigger('click');
        "; 
        Yii::app()->ClientScript->registerScript($controlId.'_script', $script, CClientScript::POS_READY);

        return $html;
    }
    
    public static function hintTextBox($name,$value='',$htmlOptions=array())
    {
        if (isset($htmlOptions['id']))
            $id = $htmlOptions['id'];
        else
            $id = CHtml::getIdByName($name);
        self::registerHintScript($id);
        
        return CHtml::textField($name,$value,$htmlOptions);
    }
    
    public static function activeHintTextBox($model,$attribute,$htmlOptions=array())
    {
        if (isset($htmlOptions['id']))
            $id = $htmlOptions['id'];
        else
            $id = CHtml::getActiveId($model,$attribute);
        self::registerHintScript($id);
        
        return CHtml::activeTextField($model,$attribute,$htmlOptions);
    }
    
    protected static function registerHintScript($id)
    {
        $baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('Core.assets.scripts.jquery.*').DIRECTORY_SEPARATOR.'jquery.hint.js');
        /**
        * @var CClientScript
        */
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($baseScriptUrl);
        $script = "jQuery('input#{$id}').hint();";
        $cs->registerScript(__CLASS__.'#Hint'.$id, $script, CClientScript::POS_READY);
    }
}
?>
