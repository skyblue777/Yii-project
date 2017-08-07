<?php
$this->breadcrumbs=array(
	'Lookups',
);

$this->menu=array(
	array('label'=>'Create Lookup', 'url'=>array('create')),
);
?>

<h1>Lookups</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
