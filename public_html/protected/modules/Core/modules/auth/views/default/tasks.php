<?php
$this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Tasks'),
);
?>

<div id="rightsTasks">

	<h3><?php echo Yii::t('AuthModule.core', 'Tasks'); ?></h3>

	<p>
		<?php echo CHtml::link(Yii::t('AuthModule.core', 'Create a new task'), array('authItem/create', 'type'=>CAuthItem::TYPE_TASK), array(
			'class'=>'addTaskLink',
		)); ?>
	</p>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Yii::t('AuthModule.core', 'No tasks found.'),
	    'htmlOptions'=>array('class'=>'taskTable'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Yii::t('AuthModule.core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'nameColumn'),
    			'value'=>'$data->nameColumn(true, true)',
    		),
    		array(
    			'name'=>'description',
    			'header'=>Yii::t('AuthModule.core', 'Description'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'descriptionColumn'),
    		),
    		array(
    			'name'=>'bizRule',
    			'header'=>Yii::t('AuthModule.core', 'Business rule'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'bizRuleColumn'),
    		),
    		array(
    			'name'=>'data',
    			'header'=>Yii::t('AuthModule.core', 'Data'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'dataColumn'),
    		),
    		array(
    			'name'=>'delete',
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'deleteColumn'),
    			'value'=>'$data->deleteTaskColumn()',
    		),
	    )
	)); ?>

	<p class="info"><?php echo Yii::t('AuthModule.core', 'Values within square brackets tell how many children each item has.'); ?></p>

</div>
