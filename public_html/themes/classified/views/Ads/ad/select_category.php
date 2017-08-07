<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category'),
);
?>

<h1 class="title"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category');?></h1>
<?php $this->widget('AdsCategoryList', array('type'=>'for_select'/*, 'ad_id'=>$ad_id*/)); ?>