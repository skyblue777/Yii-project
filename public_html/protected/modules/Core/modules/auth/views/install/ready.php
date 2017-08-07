<div id="installReady">

	<h2><?php echo Yii::t('AuthModule.install', 'Congratulations!'); ?></h2>

	<p class="greenText">
		<?php echo Yii::t('AuthModule.install', 'Rights has been installed succesfully.'); ?>
	</p>

	<p>
		<?php echo Yii::t('AuthModule.install', 'You can start by generating your authorization items') ;?>
		<?php echo CHtml::link(Yii::t('AuthModule.install', 'here'), array('authItem/generate')); ?>.
	</p>

</div>