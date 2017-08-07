<?php
$this->breadcrumbs=array(
	'Setting Params'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Create SettingParam', 'url'=>array('create')),
);
?>
<style type="text/css">
.grid-view .status-column a {cursor: text;}
</style>
<h1>Manage Setting Params</h1>

<?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'setting-param-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows'=>2,
    'selectionChanged'=>"updateSelectors",
    'afterAjaxUpdate'=>"js:enableSort",
	'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
            'value'=>'$data->id',
            'htmlOptions'=>array('width'=>'3%'/*,'class'=>'sortable'*/),
            'cssClassExpression'=>'"item-".$data->id',
        ),
		'name',
		'label',
		'value',
		'description',
        array(
            'name'=>'module',
            'type'=>'raw',
            'value'=>'$data->module',
            'filter'=>$model->getModules(),
        ),
        array(
            'name'=>'setting_group',
            'type'=>'raw',
            'value'=>'$data->setting_group',
            'filter'=>$model->getGroups(),
        ),/*
        array(
            'name'=>'ordering',
            'type'=>'raw',
            'value'=>'$data->ordering',
            'filter'=>false,
            'htmlOptions'=>array(
                'width'=>'60',
                'align'=>'center',
            )
        ),*/
        array(
            'name'=>'visible',
            'type'=>'raw',
            'value'=>'CHtml::link($data->visible ? "Yes" : "No", "#", array("class"=>($data->visible ? "active" : "")))',
            'filter'=>false,
            'htmlOptions'=>array(
                'class'=>'status-column'
            )
        ),
		array(
            'class'=>'CButtonColumn',
            'template'=>'{update} {delete}'
		),
	),
)); 

if ($grid->dataProvider->ItemCount) {
    $this->menu[] = array('label' => 'Delete selected items', 'url'=>$this->createUrl('delete'), 'linkOptions' => array('onclick'=>'return multipleDelete("setting-param-grid",this.href)'));
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);

$cs=Yii::app()->getClientScript();
$cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css');
$cs->registerCoreScript('jquery.ui');

$script = "
function initSortable() {
    //append id to tr tag
    $.each(\$( 'table.items td.sortable input'), function(){
        $(this).parents('tr').attr('id', 'ids_'+$(this).val());
    });
    
    //init sortable
    $( 'table.items' ).sortable({
        handle : 'td.sortable', 
        items : 'tr:gt(1)',
        update: function(event, ui){
            var data = $('table.items').sortable('serialize');
            $.get('".$this->createUrl('order')."', data, function(res){
            });
        }
    });
    $( 'table.items' ).disableSelection();
}
function enableSort() {
    if (\$('.filters select[name=\"SettingParam[module]\"]').val() != '' && \$('.filters select[name=\"SettingParam[setting_group]\"]').val() != '') {
        $.each(\$('.items tr:gt(1)'), function(){
            if (\$(this).find('td').length > 1)
                $(this).find('td:first').addClass('sortable');
        });
        initSortable();
    }
}
";
Yii::app()->clientScript->registerScript(__CLASS__.'#InitSortable', $script, CClientScript::POS_READY);
?>