<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Ads')=>array('list'),
);

$this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(Language::t(Yii::app()->language,'Backend.Common.Views-Ads-Update','Update Ads with ID')));
?>

<h1><?php echo Language::t(Yii::app()->language,'Backend.Ads.Update','Update Ads with ID')?> [<?php echo $model->id; ?>]</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>