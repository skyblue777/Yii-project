<?php
Yii::import('zii.widgets.jui.CJuiDatePicker');
class FJuiDatePicker extends CJuiDatePicker
{
    public function run()
    {
        list($name,$id)=$this->resolveNameID();

        if(isset($this->htmlOptions['id']))
            $id=$this->htmlOptions['id'];
        else
            $this->htmlOptions['id']=$id;
        if(isset($this->htmlOptions['name']))
            $name=$this->htmlOptions['name'];
        else
            $this->htmlOptions['name']=$name;

        if($this->hasModel())
            echo CHtml::activeTextField($this->model,$this->attribute,$this->htmlOptions);
        else
            echo CHtml::textField($name,$this->value,$this->htmlOptions);


        $options=CJavaScript::encode($this->options);

        $js = "jQuery('#{$id}').datepicker($options);";

        if (isset($this->language)){
            $this->registerScriptFile($this->i18nScriptFile);
            $js = "jQuery('#{$id}').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['{$this->language}'], {$options}));";
        }
        $js = $js."\n\$('body').ajaxSuccess(function(){".$js."})";

        $cs = Yii::app()->getClientScript();
        $cs->registerScript(__CLASS__,     $this->defaultOptions?'jQuery.datepicker.setDefaults('.CJavaScript::encode($this->defaultOptions).');':'');
        $cs->registerScript(__CLASS__.'#'.$id, $js);

    }
}