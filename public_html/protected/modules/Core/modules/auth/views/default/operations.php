<?php
$this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Operations'),
); ?>

<div id="rightsOperations">

	<h3><?php echo Yii::t('AuthModule.core', 'Operations'); ?></h3>

	<p>
		<?php 
        echo CHtml::link(Yii::t('AuthModule.core', 'Create a new operation'), array('authItem/create', 'type'=>CAuthItem::TYPE_OPERATION), array(
            'class'=>'addOperationLink',
        )); 
        echo ' | ';
        echo CHtml::link(Yii::t('AuthModule.core', 'Import pages'), array('authItem/generate'), array(
			'class'=>'addOperationLink',
		)); 
        
        ?>
	</p>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Yii::t('AuthModule.core', 'No operations found.'),
	    'htmlOptions'=>array('class'=>'operationTable'),
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
    			'value'=>'$data->deleteOperationColumn()',
    		),
	    )
	)); ?>

	<p class="info"><?php echo Yii::t('AuthModule.core', 'Values within square brackets tell how many children each item has.'); ?></p>

</div>