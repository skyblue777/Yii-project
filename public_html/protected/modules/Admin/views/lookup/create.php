<?php
$this->breadcrumbs=array(
	'Lookups'=>array('index'),
	'Create',
);

$this->menu=array(
);
?>

<h1>Create Lookup</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>