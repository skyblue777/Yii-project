<?php
$this->breadcrumbs=array(
	'Setting Params'=>array('admin'),
	$model->name=>array('update','id'=>$model->id),
	'Update',
);

$this->menu=array(
);
?>

<h1>Update SettingParam <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'modules'=>$modules)); ?>