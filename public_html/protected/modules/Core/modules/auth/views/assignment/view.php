<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Assignments'),
); ?>

<div id="rightsAssignments">

	<h3><?php echo Yii::t('AuthModule.core', 'Assignments'); ?></h3>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Yii::t('AuthModule.core', 'No users found.'),
	    'htmlOptions'=>array('class'=>'assignmentTable'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Yii::t('AuthModule.core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'nameColumn'),
    			'value'=>'$data->getAssignmentNameLink()',
    		),
    		array(
    			'name'=>'assignments',
    			'header'=>Yii::t('AuthModule.core', 'Assignments'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'assignmentColumn'),
    			'value'=>'$data->getAssignments()',
    		),
	    )
	)); ?>

</div>
