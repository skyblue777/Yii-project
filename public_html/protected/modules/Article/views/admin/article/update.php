<style type="text/css">
.errorMessage { display: none; }
</style>

<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Articles')=>array('index'),
);
if ($model->id)
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array($model->title=>array('view','id'=>$model->id),'Update'));
else
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array('Create'));

$this->menu=array(
);
?>

<h1><?php echo $model->id ? Language::t(Yii::app()->language,'Backend.Common.Common','Update') : Language::t(Yii::app()->language,'Backend.Common.Common','Create');?> <?php echo Language::t(Yii::app()->language,'Backend.Article.Admin','Article')?> <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>