<?php
/**
 * Extended ActiveForm Class File
 *
 * @author Hightman <hightman2[at]yahoo[dot]com[dot]cn>
 * @link http://www.czxiu.com/
 * @copyright hightman
 * @license http://www.yiiframework.com/license/
 * @version 1.1
 */
/*
Requirements
--------------
Yii 1.1.1 or above

Description:
--------------
This extension just extend from {link: CActiveForm} using few codes, it enhances the ajax validation.
- Submit a list of changed attributes to php backend, then can access the attributes list in Controller via $_POST['attributes']['ModelName'] and verify these attributes only.
- First to do client-side javascript validations before submission of ajax, and js validators converted from {link: CValidator} automatically.

Usage:
---------------
Using this extension is same as original CActiveForm.

~~~
[php]
$form = $this->beginWidget('ext.EActiveForm', array('enableAjaxValidation' => true));
...
$this->endWidget();
~~~

Controller example codes for verifying modified attributes only:
~~~
[php]
protected function performAjaxValidation($model)
{
  if (Yii::app()->request->isAjaxRequest && isset($_POST['ajax']))
  {
    $class = get_class($model);
    if (isset($_POST['attributes']) && is_array($_POST['attributes'][$class]))
      $attributes = $_POST['attributes'][$class];
    else
      $attributes = null;
    echo CActiveForm::validate($model, $attributes);
    Yii::app()->end();
  }
}
~~~

ChangeLog:
---------------
Nov 14, 2010
- New version number 1.1
- Strict inspection validator is completely converted.
- Add CTypeValidator support

NOTE:
---------------
For CRegualarExpressionValidator, please make sure Javascript compatible.

Reporting Issue:
-----------------
Reporting Issues and comments are welcome, plz report them to offical extension page of Yii.
[Report issue](http://www.yiiframework.com/extension/eactiveform/)
*/
Yii::import('system.web.widgets.CActiveForm', true);
class FActiveForm extends CACtiveForm
{
    public $attributesVar = 'attributes';
    
    public function init(){
        parent::init();
        $this->enableAjaxValidation = true;
        $this->clientOptions['validateOnSubmit'] = true;
        $this->clientOptions['validateOnChange'] = false;
    }
    
    public function run()
    {
        if ($this->enableAjaxValidation && $this->clientOptions['validateOnSubmit'])
        {
            // old callback func strip 'js:'
            $oldCallback = isset($this->clientOptions['beforeValidate']) ?
                substr($this->clientOptions['beforeValidate'], 3) . '(f)' : 'true';
            $newCallback = "js:function(f){var ss=f.data('settings'),em=[];";
            $newCallback .= "\$.each(ss.attributes,function(i,a){";
            $newCallback .= "if(a.beforeValidateAttribute!=undefined)a.beforeValidateAttribute(f,a,em);});";
            $newCallback .= "if(em.length>0){var c='';\$.each(em,function(i,m){c+='<li>'+m+'</li>';});";
            $newCallback .= "\$('#'+ss.summaryID+' ul').html(c);$('#'+ss.summaryID).toggle(true);}";
            // restore ajaxVar (maybe changed on beforeValidateAttribute.
            $newCallback .= "ss.ajaxVar='" . $this->getAjaxVar() . "';";
            $newCallback .= "return em.length>0?false:" . $oldCallback . ";}";
            $this->clientOptions['beforeValidate'] = $newCallback;
        }
        parent::run();
    }

    public function error($model, $attribute, $htmlOptions = array(), $enableAjaxValidation = true)
    {
        if ($this->enableAjaxValidation && $enableAjaxValidation)
        {
            $full = true;
            $codes = $this->getValidatorCodes($model, $attribute, $full);
            // old callback func strip 'js:'
            $oldCallback = isset($htmlOptions['beforeValidateAttribute']) ?
                substr($htmlOptions['beforeValidateAttribute'], 3) . '(f,a)' : 'true';
            $newCallback = "js:function(f,a,m){";
            if (count($codes) > 0)
            {
                $newCallback .= "var val=\$('#'+a.inputID).val(),em='';";
                $newCallback .= implode('else ', $codes);
                $newCallback .= "if(em!=''){var e=\$('#'+a.errorID),c=\$.fn.yiiactiveform.getInputContainer(a);";
                $newCallback .= "if(m!=undefined){m.push(em);if(m.length==1)\$('#'+a.inputID).focus();}";
                $newCallback .= "c.removeClass(a.validatingCssClass).removeClass(a.successCssClass).addClass(a.errorCssClass);";
                $newCallback .= "e.html(em);if(!a.hideErrorMessage)e.toggle(true);return false;}";
                // validator full converted, need not send to backend
                if ($full === true)
                {
                    $newCallback .= "if(m==undefined){var e=\$('#'+a.errorID),c=\$.fn.yiiactiveform.getInputContainer(a);";
                    $newCallback .= "c.removeClass(a.validatingCssClass).removeClass(a.errorCssClass).addClass(a.successCssClass);";
                    $newCallback .= "if(!a.hideErrorMessage)e.toggle(false);return false;}";
                }
            }
            // submit changed attributes by modifing settings.ajaxVar
            $newCallback .= "if(m==undefined){var v='';";
            $newCallback .= "\$.each(f.data('settings').attributes,function(){";
            $newCallback .= "if(this.status==2)v+=encodeURIComponent('" . $this->attributesVar . "['+this.model+'][]')+'='+encodeURIComponent(this.name)+'&';});";
            $newCallback .= "v+='" . $this->getAjaxVar() . "';f.data('settings').ajaxVar=v;}";
            // return the result or old callback
            $newCallback .= "return " . $oldCallback . ";}";
            $htmlOptions['beforeValidateAttribute'] = $newCallback;
        }
        return parent::error($model, $attribute, $htmlOptions, $enableAjaxValidation);
    }

    /**
     * Get javascript validator code for the attribute of model
     * @param CModel $model
     * @param string $attribute
     * @param boolean $full
     * @return array js codes list
     */
    private function getValidatorCodes($model, $attribute, &$full)
    {
        $checkCodes = array();
        $full = true;
        foreach ($model->getValidators($attribute) as $validator)
        {
            $code = '';
            $message = '';
            switch (get_class($validator))
            {
                case 'CRequiredValidator' :
                    if ($validator->requiredValue !== null)
                    {
                        $value = strval($validator->requiredValue);
                        $code = "val!='" . CJavaScript::quote($value) . "'";
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} must be {value}.', array('{value}' => $value));
                    }
                    else
                    {
                        $code = '!val';
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} cannot be blank.');
                    }
                    break;
                case 'CRegularExpressionValidator':
                    $pattern = $validator->pattern;
                    $pattern = preg_replace('/\\\\x\{?([0-9a-fA-F]+)\}?/', '\u$1', $pattern);
                    $delim = substr($pattern, 0, 1);
                    $endpos = strrpos($pattern, $delim, 1);
                    $flag = substr($pattern, $endpos + 1);
                    if ($delim !== '/')
                        $pattern = '/' . str_replace('/', '\\/', substr($pattern, 1, $endpos - 1)) . '/';
                    else
                        $pattern = substr($pattern, 0, $endpos + 1);
                    if (!empty($flag))
                        $pattern .= preg_replace('/[^igm]/', '', $flag);
                    $code = $validator->allowEmpty ? 'val && ' : '';
                    $code .= $validator->not ? '' : '!';
                    $code .= 'val.match(' . $pattern . ')';
                    $message = $validator->message !== null ?
                        $validator->message : Yii::t('yii', '{attribute} is invalid.');
                    break;
                case 'CEmailValidator' :
                    $code = $validator->allowEmpty ? 'val && ' : '';
                    $code .= '!val.match(' . ($validator->allowName ? $validator->fullPattern : $validator->pattern) . ')';
                    $message = $validator->message !== null ?
                        $validator->message : Yii::t('yii', '{attribute} is not a valid email address.');
                    break;
                case 'CUrlValidator' :
                    $code = $validator->allowEmpty ? 'val && ' : '';
                    $code .= '!val.match(' . $validator->pattern . ')';
                    $message = $validator->message !== null ?
                        $validator->message : Yii::t('yii', '{attribute} is not a valid URL.');
                    break;
                case 'CCompareValidator' :
                    if ($validator->compareValue !== null)
                    {
                        $compareTo = $validator->compareValue;
                        $compareValue = CJavaScript::encode($validator->compareValue);
                    }
                    else
                    {
                        $compareAttribute = $validator->compareAttribute === null ? $attribute . '_repeat' : $validator->compareAttribute;
                        $compareValue = "\$('#" . (CHtml::activeId($model, $compareAttribute)) . "').val()";
                        $compareTo = $model->getAttributeLabel($compareAttribute);
                    }

                    $code = $validator->allowEmpty ? 'val && ' : '';
                    switch ($validator->operator)
                    {
                        case '=' :
                        case '==' :
                            $code .= 'val!=' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must be repeated exactly.');
                            break;
                        case '!=' :
                            $code .= 'val==' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must not be equal to "{compareValue}".', array('{compareValue' => $compareTo));
                            break;
                        case '>' :
                            $code .= 'val<=' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must be greater than "{compareValue}".', array('{compareValue' => $compareTo));
                            break;
                            break;
                        case '>=' :
                            $code .= 'val<' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must be greater than or equal to "{compareValue}".', array('{compareValue' => $compareTo));
                            break;
                            break;
                        case '<' :
                            $code .= 'val>=' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must be less than "{compareValue}".', array('{compareValue' => $compareTo));
                            break;
                            break;
                        case '<=' :
                            $code .= 'val>' . $compareValue;
                            $message = $validator->message !== null ?
                                $validator->message : Yii::t('yii', '{attribute} must be less than or equal to "{compareValue}".', array('{compareValue' => $compareTo));
                            break;
                            break;
                        default :
                            $code = '';
                    }
                    break;
                case 'CStringValidator' :
                    if ($validator->min !== null)
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= 'val.length<' . $validator->min;
                        $message = $validator->tooShort !== null ?
                            $validator->tooShort : Yii::t('yii', '{attribute} is too short (minimum is {min} characters).', array('{min}' => $validator->min));

                        $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                        $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
                    }
                    if ($validator->max !== null)
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= 'val.length>' . $validator->max;
                        $message = $validator->tooLong !== null ?
                            $validator->tooLong : Yii::t('yii', '{attribute} is too long (maximum is {max} characters).', array('{max}' => $validator->max));

                        $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                        $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
                    }
                    if ($validator->is !== null)
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= 'val.length!=' . $validator->is;
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} is of the wrong length (should be {length} characters).', array('{length}' => $validator->is));

                        $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                        $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
                    }
                    $code = '';
                    break;
                case 'CRangeValidator' :
                    if (!is_array($validator->range))
                        break;
                    $range = CJavaScript::encode($validator->range);
                    $message = $validator->message !== null ?
                        $validator->message : Yii::t('yii', '{attribute} is ' . ($validator->not ? '' : 'not ') . 'in the list.');
                    $code = $validator->allowEmpty ? 'val && ' : '';
                    $code .= $validator->not ? '' : '!';
                    $code .= '$.inArray(val,' . CJavaScript::encode($validator->range) . ')';
                    break;
                case 'CNumberValidator' :
                    if ($validator->min !== null)
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= 'val<' . $validator->min;
                        $message = $validator->tooSmall !== null ?
                            $validator->tooSmall : Yii::t('yii', '{attribute} is too small (minimum is {min}).', array('{min}' => $validator->min));

                        $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                        $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
                    }
                    if ($validator->max !== null)
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= 'val>' . $validator->max;
                        $message = $validator->tooMax !== null ?
                            $validator->tooMax : Yii::t('yii', '{attribute} is too big (maximum is {max}).', array('{max}' => $validator->max));

                        $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                        $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
                    }

                    $code = $validator->allowEmpty ? 'val && ' : '';
                    if ($validator->integerOnly)
                    {
                        $code .= '!val.match(/^\s*[+-]?\d+\s*$/)';
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} must be a integer.');
                    }
                    else
                    {
                        $code .= '!val.match(/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/)';
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} must be a number.');
                    }
                    break;
                case 'CBooleanValidator' :
                    $code = $validator->allowEmpty ? 'val && ' : '';
                    $code .= 'val != ' . $validator->trueValue . ' && val != ' . $validator->falseValue;
                    $message = $validator->message !== null ?
                        $validator->message : Yii::t('yii', '{attribute} must be either {true} or {false}.', array('{true}' => $validator->trueValue, 'false' => $validator->falseValue));
                    break;
                case 'CTypeValidator' :
                    if ($validator->type === 'integer')
                        $regexp = '/^[-+]?[0-9]+$/';
                    else if ($validator->type === 'float')
                        $regexp .= '/^[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/';
                    else if ($validator->type === 'date')
                        $regexp = $this->getDateTimeRegexp($validator->dateFormat);
                    else if ($validator->type === 'time')
                        $regexp = $this->getDateTimeRegexp($validator->timeFormat);
                    else if ($validator->type === 'datetime')
                        $regexp = $this->getDateTimeRegexp($validator->datetimeFormat);
                    else
                    {
                        $regexp = '';
                        $full = false;
                    }
                    if ($regexp !== '')
                    {
                        $code = $validator->allowEmpty ? 'val && ' : '';
                        $code .= '!val.match(' . $regexp . ')';
                        $message = $validator->message !== null ?
                            $validator->message : Yii::t('yii', '{attribute} must be {type}.', array('{type}' => $validator->type));
                    }
                    break;
                default :
                    $full = false;
                    break;
            }
            if ($code !== '')
            {
                $message = str_replace('{attribute}', $model->getAttributeLabel($attribute), $message);
                $checkCodes[] = "if(" . $code . ")em='" . CJavaScript::quote($message) . "';";
            }
        }
        return $checkCodes;
    }

    /**
     * get the ajax var name on self::clientOptions
     * @return string
     */
    private function getAjaxVar()
    {
        return isset($this->clientOptions['ajaxVar']) ? $this->clientOptions['ajaxVar'] : 'ajax';
    }

    /**
     * get datetime pattern by format
     * @param string $format
     * @return string pattern
     */
    private function getDateTimeRegexp($format)
    {
        // MM/dd/yyyy hh:mm:ss a
        $format = preg_quote($format, '/');
        $format = preg_replace('/[yMdhms]/', '\d', $format);
        $format = str_replace('a', '(?:am|pm|AM|PM)', $format);
        return '/' . $format . '/';
    }
}