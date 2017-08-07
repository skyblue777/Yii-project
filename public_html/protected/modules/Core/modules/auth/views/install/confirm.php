<div id="installConfirm">

	<h2><?php echo Yii::t('AuthModule.install', 'Install Rights'); ?></h2>

	<p class="redText">
		<?php echo Yii::t('AuthModule.install', 'Rights is already installed!'); ?>
	</p>

	<p><?php echo Yii::t('AuthModule.install', 'Please confirm if you wish to reinstall.'); ?></p>

	<p>
		<?php echo CHtml::link(Yii::t('AuthModule.install', 'Yes'), array('install/run', 'confirm'=>1)); ?> /
		<?php echo CHtml::link(Yii::t('AuthModule.install', 'No'), Yii::app()->homeUrl); ?>
	</p>

	<p class="info"><?php echo Yii::t('AuthModule.install', 'Notice: All your existing data will be lost.'); ?></p>

</div>