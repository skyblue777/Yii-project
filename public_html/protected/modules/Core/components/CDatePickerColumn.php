<?php
Yii::import('zii.widgets.grid.CGridColumn');
class CDatePickerColumn extends CDataColumn {
    public $fromOptions = array();
    public $fromHtmlOptions = array();
    public $toOptions = array();
    public $toHtmlOptions = array();
    public $linkHtmlOptions = array();
    public $template = '{link}<div style="display:none; width: 220px;">{from}-{to}</div>';
    
    /**
     * Renders the filter cell content.
     * This method will render the {@link filter} as is if it is a string.
     * If {@link filter} is an array, it is assumed to be a list of options, and a dropdown selector will be rendered.
     * Otherwise if {@link filter} is not false, a text field is rendered.
     * @since 1.1.1
     */
    protected function renderFilterCellContent()
    {
        if($this->filter!==false && $this->grid->filter!==null && $this->name!==null)
        {
            $data = $this->grid->filter->{$this->name};
            if ((is_array($data) && isset($data['from'], $data['to'])) === false) {
                $data['from'] = '';
                $data['to'] = '';
            }
            $defaultOptions = array(
                'dateFormat'=>'dd/mm/yy',
                'minDate'=>'-10y',
                'maxDate'=>'js:new Date()',
                'changeMonth'=>true,
                'changeYear'=>true,
            );
            $defaultOptions['beforeShow'] = 'js:function(input, inst) {
    var maxDate = $(input).siblings(".hasDatepicker").val();
    maxDate = $.datepicker.parseDate(\''.$defaultOptions['dateFormat'].'\', maxDate);
    if (maxDate)
        $(inst.input).datepicker("option", "maxDate", maxDate);
}';
            $options = CMap::mergeArray($defaultOptions, $this->fromOptions);
            $defaultHtmlOptions = array(
                'value'=>$data['from'],
                'size'=>'10'
            );
            $htmlOptions = CMap::mergeArray($defaultHtmlOptions, $this->fromHtmlOptions);
            $id = isset($htmlOptions['id']) ? $htmlOptions['id'] : null;
            $htmlOptions = array(
                'id'=>$id,
                'model'=>$this->grid->filter,
                'attribute'=>$this->name."[from]",
                'options'=>$options,
                'htmlOptions'=>$htmlOptions,
            );
            ob_start();
            Yii::app()->controller->widget('application.modules.Core.components.FJuiDatePicker', $htmlOptions);
            $fromContent = ob_get_contents();
            ob_clean();
            ob_end_clean();
            $defaultOptions['beforeShow'] = 'js:function(input, inst) {
    var minDate = $(input).siblings(".hasDatepicker").val();
    minDate = $.datepicker.parseDate(\''.$defaultOptions['dateFormat'].'\', minDate);
    if (minDate)
        $(inst.input).datepicker("option", "minDate", minDate);
}';
            $options = CMap::mergeArray($defaultOptions, $this->toOptions);
            $defaultHtmlOptions = array(
                'value'=>$data['to'],
                'size'=>'10'
            );
            $htmlOptions = CMap::mergeArray($defaultHtmlOptions, $this->toHtmlOptions);
            $id = isset($htmlOptions['id']) ? $htmlOptions['id'] : null;
            $htmlOptions = array(
                'id'=>$id,
                'model'=>$this->grid->filter,
                'attribute'=>$this->name."[to]",
                'options'=>$options,
                'htmlOptions'=>$htmlOptions,
            );
            ob_start();
            Yii::app()->controller->widget('application.modules.Core.components.FJuiDatePicker', $htmlOptions);
            $toContent = ob_get_contents();
            ob_clean();
            ob_end_clean();
            $class = empty($this->grid->filter->{$this->name}['from']) === true && empty($this->grid->filter->{$this->name}['to']) === true ? 'filter-date' : 'filter-date active';
            $linkHtmlOptions = CMap::mergeArray(array(
                'class'=>$class,
                'title'=>'Set Filter',
            ), $this->linkHtmlOptions);
            $link = CHtml::link('Set Filter', '#', $linkHtmlOptions);
            echo strtr($this->template,array('{from}'=>$fromContent, '{to}'=>$toContent, '{link}'=>$link));
            $script = "
$('#{$this->grid->id} .filters .".$linkHtmlOptions['class']."').live('click', function(){
    $(this).siblings('div').toggle();
    return false;
});
";
            Yii::app()->clientScript->registerScript(__CLASS__.'#ShowFilterCal', $script, CClientScript::POS_READY);
        }
        else
            parent::renderFilterCellContent();
    }
}