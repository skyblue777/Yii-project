<?php
$this->breadcrumbs=array(
	'Lookups'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Create Lookup', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('lookup-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Lookups</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'lookup-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows'=>2,
    'selectionChanged'=>"updateSelectors",
	'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
            'value'=>'$data->id',
            'htmlOptions'=>array('width'=>'3%'),
        ),
		'id',
		'name',
		'code',
		'type',
		'position',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); 

if ($grid->dataProvider->ItemCount) {
    $this->menu[] = array('label' => 'Delete selected items', 'url'=>$this->createUrl('delete'), 'linkOptions' => array('onclick'=>'return multipleDelete("lookup-grid",this.href)'));
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);
?>