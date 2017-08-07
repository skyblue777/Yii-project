<?php
/**
* $Id$
* @author Hung Nguyen, Flexica Solutions
* 
* The BulkAction widget works with tabular data sheet to provide users ranges of actions he can
* perform on selected items. It render a drop-down list whose each option is an action user can choose.
* 
* Required params: array Actions, each item can be a string or an array. 
* You can specify 'Actions' => array('action 1','action 2','action 3') OR
* array(
*   'key 1' => array('action 1','JS code to be performed'),
*   'key 2' => array('action 2','JS code to be performed'),
*   ...
* )
* 
* By default, ActionOnSelect is set to TRUE and the js code is perform if user select the option.
* If ActionOnSelect is set to FALSE then JS code is ignored, you should use a simple array of 
* actions to keep code clean.
* 
* Set Prompt property for a prompt text. Default is "-- perform bulk action --"
* Set Label property for the leading text. Default is "With selected items"
* If you have more than 1 BulkAction widgets on your page for different tabular data sheets, 
* you have to set the Name property of each widget to have them work properly.
*/
class BulkAction extends CWidget
{
    public $grid;
    /**
    * The actions to be listed in drop-down
    * 
    * Simple form:
    * array('action 1','action 2','action 3')
    * 
    * Full option form 
    * array(
    *   'key 1' => array('action 1','url'),
    *   'key 2' => array('action 2','url'),
    * )
    * @var array
    */
    protected $actions = array();

    /**
    * Prompt text in the drop-down list
    * @var string
    */
    protected $prompt = '-- perform bulk action --';

    /**
    * Leadding text before the drop-down list
    * @var string
    */
    protected $label = 'With selected items';

    /**
    * Control name, used to reander drop-down list name and id attributes
    * @var string
    */
    protected $name = 'BulkActions';
    
    public $buttonText = 'Go';
    public $buttonOptions = array();

    /**
    * Whether to perform JS code immediately when user chooses an action
    * @var bool
    */
    protected $actionOnSelect = true;
    protected $callbacks = array();
    
    public function setActions($value){$this->actions = $value; }
    public function setName($value){ $this->name = $value; }
    public function setLabel($value){ $this->label = $value; }
    public function setPrompt($value){ $this->prompt = $value; }
    public function setActionOnSelect($value){ $this->actionOnSelect = $value; }
    
    public function run(){
        if ((is_array($this->actions) === true && count($this->actions)) === false) return;
            
        $html = $this->label;
        $listData = array('' => $this->prompt);
        foreach($this->actions as $key => $action){
            if (is_string($action))
                $listData[$key] = $action;
            elseif(is_array($action)){
                $listData[$key] = $action[0];
                $this->callbacks[$key] = @$action[1];
            }
        }
        $html .= ' ' . FHtml::dropDownList($this->name,'',$listData, array('id' => $this->name));
        
        echo $html;
        $this->buttonOptions = CMap::mergeArray(array('class'=>'bulk-button'), $this->buttonOptions);
        echo CHtml::submitButton($this->buttonText, $this->buttonOptions);
        
        if ($this->actionOnSelect && !empty($this->callbacks)){
            $this->registerClientScript();
        }
    }
    
    protected function registerClientScript(){
        $cs=Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $case = '';
        foreach($this->callbacks as $key => $url){
            if (!empty($url))
                $case .= "case '$key': bulkAction('{$url}'); break;\n";
        }
        
        $id = $this->grid->id;
        $cs->registerScript(__CLASS__.'#bulkAction.button',"
$('.bulk-button').live('click',function(){
    switch (\$('#".CHtml::getIdByName($this->name)."').val()){
        {$case}
        default:
            alert('Please select action');
    }
    return false;
});
function bulkAction(url){
    var items = $('td.checkbox-column input:checked');
    if (items.length) {
        if (\$('#".CHtml::getIdByName($this->name)."').val() == 'delete')
            if(!confirm('Are you sure you want to delete this item?')) return false;
        var id = [];
        items.each(function(){id.push(this.value);});
        $.fn.yiiGridView.update('{$id}', {
            'type':'POST',
            'url':url,
            'data':{'Id': id, 'action': \$('#".CHtml::getIdByName($this->name)."').val()},
            'success':function() {
                $.fn.yiiGridView.update('{$id}', {cacheResponse:false});
            }
        });
    } else {
        alert('Please select at least 1 item');
    }
    return false;
};
        ", CClientScript::POS_READY);
    }
}
?>
