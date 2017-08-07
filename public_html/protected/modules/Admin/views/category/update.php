<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Categories')=>array('admin'),
);
if ($model->id)
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array($model->title=>array('update','id'=>$model->id),Language::t(Yii::app()->language,'Backend.Common.Common','Update')));
else
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(Language::t(Yii::app()->language,'Backend.Common.Common','Create')));
?>

<h1><?php echo $model->title ? Language::t(Yii::app()->language,'Backend.Common.Common','Update') : Language::t(Yii::app()->language,'Backend.Common.Common','Create');?> <?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Category')?> <?php echo $model->title; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>