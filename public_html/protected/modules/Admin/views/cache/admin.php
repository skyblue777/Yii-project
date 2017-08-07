<?php
$this->breadcrumbs=array(
	'Caches'=>array('index'),
	'Manage',
);

?>

<h1>Manage Caches</h1>

<?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cache-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows'=>2,
    'selectionChanged'=>"js:function(){}",
	'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
            'value'=>'$data->id',
            'htmlOptions'=>array('width'=>'3%', 'class'=>'checkbox-column'),
        ),
        'name',
        'description',
        array(
            'header'=>'Force Rebuild Cache',
            'name'=>'expired',
            'value'=>'Yii::app()->getDateFormatter()->formatDateTime(strtotime($data->expired))',
            'filter'=>false,
            'htmlOptions'=>array('class'=>'datetime-column'),
        ),
        array(
            'header'=>'Cache Time(second)',
            'name'=>'duration',
            'value'=>'$data->duration == -1 ? "Use Global" : $data->duration',
            'filter'=>false,
            'htmlOptions'=>array('width'=>'150', 'align'=>'center'),
        ),
		array(
            'class'=>'CButtonColumn',
			'template'=>'{expire} {update}',
            'buttons'=>array(
                'expire'=>array(
                    'label'=>'Rebuild Cache',
                    'url'=>'Yii::app()->controller->createUrl("expire", array("ids[]"=>$data->id))',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/images/rebuild-cache.png',
                    'options'=>array(
                        'class'=>'rebuild-cache',
                    )
                )
            )
		),
	),
)); 

if ($grid->dataProvider->ItemCount) {
    $this->menu[] = array('label' => 'Rebuild All', 'url'=>$this->createUrl('expire'), 'linkOptions' => array('class'=>'rebuild-all-cache'));
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);
$script = "
$('.button-column a.rebuild-cache').live('click', function(){
    if(!confirm('".Yii::t('zii','Are you sure you want to delete this item?')."')) return false;
    $.get(\$(this).attr('href'), function(){
        $.fn.yiiGridView.update('cache-grid');
    });
    return false;
});
$('.rebuild-all-cache').live('click', function(){
    var data = [];
    $.each(\$('td.checkbox-column input:checked'), function(index, value){
        data.push('ids[]='+\$(this).val());
    });
    if (data.length == 0) {
        alert('".Yii::t('zii','No item seleted.')."');
        return false;
    }
    if(!confirm('".Yii::t('zii','Are you sure you want to delete this item?')."')) return false;
    var url = \$(this).attr('href');
    $.get(url, data.join('&'), function(){
        $.fn.yiiGridView.update('cache-grid');
    });
    return false;
});
";
Yii::app()->clientScript->registerScript(__CLASS__.'#RebuildCache', $script, CClientScript::POS_READY);
?>