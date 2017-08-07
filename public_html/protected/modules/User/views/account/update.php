<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Users')=>array('index'),
);

if ($model->id)
    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array($model->username=>array('update','id'=>$model->id),Language::t(Yii::app()->language,'Backend.Common.Common','Update')));
else
    $this->breadcrumbs[] = Language::t(Yii::app()->language,'Backend.Common.Common','Create');

?>

<?php if ($model->IsNewRecord) : ?>
    <h1><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Create')?> <?php if ($this->action->id=='createAdmin') echo Language::t(Yii::app()->language,'Backend.Common.Common','Administrator'); else echo Language::t(Yii::app()->language,'Backend.Common.Common','User'); ?></h1>
<?php else : ?>
    <h1><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Update')?> <?php echo $model->username; ?></h1>
<?php endif; ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>