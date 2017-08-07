<?php
$this->breadcrumbs=array(
	'Article Comments'=>array('index'),
);
if ($model->id)
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array($model->name=>array('view','id'=>$model->id),'Update'));
else
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array('Create'));

$this->menu=array(
);
?>

<h1><?php echo $model->id ? 'Update' : 'Create';?> ArticleComment <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>