<?php
$this->breadcrumbs=array(
	'Article Comments',
);

$this->menu=array(
	array('label'=>'Create ArticleComment', 'url'=>array('create')),
);
?>

<h1>Article Comments</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
