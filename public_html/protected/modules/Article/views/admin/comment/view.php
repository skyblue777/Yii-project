<?php
$this->breadcrumbs=array(
	'Article Comments'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create ArticleComment', 'url'=>array('create')),
	array('label'=>'Update ArticleComment', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ArticleComment', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1>View ArticleComment #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'article_id',
		'user_id',
		'name',
		'email',
		'url',
		'comment',
		'status',
		'create_time',
		'update_time',
	),
)); ?>
