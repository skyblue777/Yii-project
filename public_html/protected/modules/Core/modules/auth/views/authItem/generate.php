<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Yii::t('AuthModule.core', 'Auth Item Generator'),
); ?>

<h3><?php echo Yii::t('AuthModule.core', 'Add Pages into Access Control'); ?></h3>

<div class="form">

	<?php $form=$this->beginWidget('CActiveForm'); ?>

		<div class="row">

            <p><?php echo Yii::t('AuthModule.core', 'Click the controller you want to manage permissions. You can import pages into access control by checking the page actions and choosing Add selected page.'); ?></p>

			<table class="generateItemTable" border="0" cellpadding="0" cellspacing="0">

				<tbody>

					<tr class="applicationHeadingRow">
						<th colspan="3"><?php echo Yii::t('AuthModule.core', 'Application'); ?></th>
					</tr>

					<?php $this->renderPartial('_generateItems', array(
						'model'=>$model,
						'form'=>$form,
						'items'=>$items,
						'existingItems'=>$existingItems,
						'showModuleHeadingRow'=>true,
						'basePathLength'=>strlen(Yii::app()->basePath),
					)); ?>

				</tbody>

			</table>

		</div>

		<div class="row">

   			<?php echo CHtml::link(Yii::t('AuthModule.core', 'Select all'), '#', array(
   				'onclick'=>"jQuery('.generateItemTable').find(':checkbox').attr('checked', 'checked'); return false;",
   				'class'=>'selectAllLink')); ?>
   			/
			<?php echo CHtml::link(Yii::t('AuthModule.core', 'Select none'), '#', array(
				'onclick'=>"jQuery('.generateItemTable').find(':checkbox').removeAttr('checked'); return false;",
				'class'=>'selectNoneLink')); ?>

		</div>

   		<div class="row">

			<?php echo CHtml::submitButton(Yii::t('AuthModule.core', 'Add selected page')); ?>

		</div>

	<?php $this->endWidget(); ?>

</div>