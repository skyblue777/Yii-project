<?php
$this->breadcrumbs=array(
	'Setting Params'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create SettingParam', 'url'=>array('create')),
	array('label'=>'Update SettingParam', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete SettingParam', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1>View SettingParam #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'label',
		'value',
		'description',
		'group',
		'ordering',
		'visible',
		'module',
	),
)); ?>
