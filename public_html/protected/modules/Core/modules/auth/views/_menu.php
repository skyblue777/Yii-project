<?php $this->widget('zii.widgets.CMenu', array(
	'items'=>array(
		array(
			'label'=>Yii::t('AuthModule.core', 'Permissions'),
			'url'=>array('default/permissions'),
			'itemOptions'=>array('class'=>'permissions'),
		),
		array(
			'label'=>Yii::t('AuthModule.core', 'Operations'),
			'url'=>array('default/operations'),
			'itemOptions'=>array('class'=>'operations'),
		),
		array(
			'label'=>Yii::t('AuthModule.core', 'Tasks'),
			'url'=>array('default/tasks'),
			'itemOptions'=>array('class'=>'tasks'),
		),
		array(
			'label'=>Yii::t('AuthModule.core', 'Roles'),
			'url'=>array('default/roles'),
			'itemOptions'=>array('class'=>'roles'),
		),
		array(
			'label'=>Yii::t('AuthModule.core', 'Assignments'),
			'url'=>array('assignment/view'),
			'itemOptions'=>array('class'=>'assignments'),
		),
		array(
			'label'=>Yii::t('AuthModule.core', 'Generator'),
			'url'=>array('authItem/generate'),
			'itemOptions'=>array('class'=>'generator'),
		),
	)
));	?>
