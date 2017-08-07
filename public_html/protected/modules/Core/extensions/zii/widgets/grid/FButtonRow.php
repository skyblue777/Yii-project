<?php
Yii::import('zii.widgets.grid.CButtonColumn');
class FButtonRow extends CButtonColumn
{
    public $template='{update} | {delete}';
    public $updateButtonLabel='Edit';
    public $updateButtonUrl='Yii::app()->controller->createUrl("edit", array("Id"=>$data->primaryKey))';
    public $updateButtonOptions=array('class'=>'editUser');
    public $deleteButtonUrl='Yii::app()->controller->createUrl("bulk", array("Id"=>$data->primaryKey,"action"=>"delete"))';
    public $deleteButtonOptions=array('class'=>'deleteUser');
    
    public function init()
    {
        $this->updateButtonImageUrl='';//Yii::app()->theme->BaseUrl.'/images/ico-edit.gif';
        $this->deleteButtonImageUrl='';//Yii::app()->theme->BaseUrl.'/images/ico-delete.gif';
        parent::init();
        if (isset($this->buttons['update'])) {
            $update = $this->buttons['update'];
            unset($update['imageUrl']);
            $this->buttons['update'] = $update;
        }
        if (isset($this->buttons['delete'])) {
            $delete = $this->buttons['delete'];
            unset($delete['imageUrl']);
            $this->buttons['delete'] = $delete;
        }
    }

    /**
     * Renders a data cell.
     * @param integer the row number (zero-based)
     */
    public function renderDataCell($row)
    {
        $data=$this->grid->dataProvider->data[$row];
        $options=$this->htmlOptions;
        if($this->cssClassExpression!==null)
        {
            $class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
            if(isset($options['class']))
                $options['class'].=' '.$class;
            else
                $options['class']=$class;
        }
        echo CHtml::openTag('div',$options);
        $this->renderDataCellContent($row,$data);
        echo '</div>';
    }

    /**
     * Initializes the default buttons (view, update and delete).
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();
        if(is_string($this->deleteConfirmation))
            $confirmation="if(!confirm(".CJavaScript::encode($this->deleteConfirmation).")) return false;";
        else
            $confirmation='';
        
        if(Yii::app()->request->enableCsrfValidation)
        {
            $csrfTokenName = Yii::app()->request->csrfTokenName;
            $csrfToken = Yii::app()->request->csrfToken;
            $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
        }
        else
            $csrf = '';
        
        $this->buttons['delete']['click']=<<<EOD
function() {
    $confirmation
    $.fn.yiiGridView.update('{$this->grid->id}', {
        type:'POST',
        url:$(this).attr('href'),$csrf
        success:function() {
            $.fn.yiiGridView.update('{$this->grid->id}', {cacheResponse:false});
        }
    });
    return false;
}
EOD;
    }
}