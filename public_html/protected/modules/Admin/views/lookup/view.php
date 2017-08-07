<?php
$this->breadcrumbs=array(
	'Lookups'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create Lookup', 'url'=>array('create')),
	array('label'=>'Update Lookup', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Lookup', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1>View Lookup #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'code',
		'type',
		'position',
	),
)); ?>
