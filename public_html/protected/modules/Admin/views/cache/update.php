<?php
$this->breadcrumbs=array(
	'Caches'=>array('index'),
);
if ($model->id)
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array($model->name=>array('update','id'=>$model->id),'Update'));
else
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array('Create'));
?>

<h1><?php echo $model->id ? 'Update' : 'Create';?> Cache <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>