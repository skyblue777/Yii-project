<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Assignments')=>array('assignment/view'),
	$model->getName(),
); ?>

<div id="userAssignments" class="span-12 first">

	<h3><?php echo Yii::t('AuthModule.core', 'Assignments for :username', array(':username'=>$model->getName())); ?></h3>

    <?php echo FHtml::link(Yii::t('AuthModule','Edit this user'), $this->createUrl('/Core/user/edit', array('Id' => $this->get('id',0)))) ; ?>
    
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$dataProvider,
		'template'=>'{items}',
		'emptyText'=>Yii::t('AuthModule.core', 'This user has not been assigned any authorization items.'),
		'htmlOptions'=>array('class'=>'miniTable userAssignmentTable'),
		'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Yii::t('AuthModule.core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'nameColumn'),
    			'value'=>'$data->nameColumn()',
    		),
    		array(
    			'name'=>'type',
    			'header'=>Yii::t('AuthModule.core', 'Type'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'typeColumn'),
    			'value'=>'$data->typeColumn()',
    		),
    		array(
    			'name'=>'revoke',
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'revokeColumn'),
    			'value'=>'$data->revokeAssignmentColumn()',
    		),
		)
	)); ?>

</div>

<div id="addUserAssignment" class="span-11 last">

	<?php if( $form!==null ): ?>

   		<div><?php echo Yii::t('AuthModule.core', 'Add Assignment'); ?></div>

		<div class="form">

			<?php echo $form->render(); ?>

		</div>

	<?php endif; ?>

</div>
