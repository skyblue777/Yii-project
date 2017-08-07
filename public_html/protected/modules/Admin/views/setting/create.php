<?php
$this->breadcrumbs=array(
	'Setting Params'=>array('index'),
	'Create',
);

$this->menu=array(
);
?>

<h1>Create SettingParam</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>