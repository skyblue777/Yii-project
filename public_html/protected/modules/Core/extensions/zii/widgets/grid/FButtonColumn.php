<?php
Yii::import('zii.widgets.grid.CButtonColumn');
class FButtonColumn extends CButtonColumn
{
    public $template='{update} {delete}';
    public $updateButtonUrl='Yii::app()->controller->createUrl("edit", array("Id"=>$data->primaryKey))';
    public $updateButtonOptions=array('class'=>'editUser');
    public $deleteButtonUrl='Yii::app()->controller->createUrl("bulk", array("Id"=>$data->primaryKey,"action"=>"delete"))';
    public $deleteButtonOptions=array('class'=>'deleteUser');
    
    public function init()
    {
        $this->updateButtonImageUrl=Yii::app()->theme->BaseUrl.'/images/ico-edit.gif';
        $this->deleteButtonImageUrl=Yii::app()->theme->BaseUrl.'/images/ico-delete.gif';
        parent::init();
    }
}