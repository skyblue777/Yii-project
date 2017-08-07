<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Create :type', array(':type'=>Rights::getAuthItemTypeName($_GET['type']))),
); ?>

<div class="authItem">

	<h3><?php echo Yii::t('AuthModule.core', 'Create :type', array(':type'=>Rights::getAuthItemTypeName($_GET['type']))); ?></h3>

	<div class="form">

		<?php echo $form->render(); ?>

	</div>

</div>