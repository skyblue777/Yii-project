<?php $this->breadcrumbs = array(
	'Rights'=>Rights::getBaseUrl(),
	Rights::getAuthItemTypeNamePlural($model->type)=>array(Rights::getAuthItemRoute($model->type)),
	Rights::beautifyName($model->name),
); ?>

<div class="authItem">

	<div class="form span-12 first">

		<h3><?php echo Yii::t('AuthModule.core', 'Update :name', array(':name'=>Rights::beautifyName($model->name))); ?> - <?php echo Rights::getAuthItemTypeName($model->type); ?></h3>

		<?php echo $form->render(); ?>

	</div>

	<div id="authItemRelations" class="span-11 last">

		<?php if( $model->name!==Rights::module()->superuserName ): ?>

			<div id="authItemParents">

				<div><?php echo Yii::t('AuthModule.core', 'Parents'); ?></div>

				<?php $this->widget('zii.widgets.grid.CGridView', array(
					'dataProvider'=>$parentDataProvider,
					'template'=>'{items}',
					'emptyText'=>Yii::t('AuthModule.core', 'This item has no parents.'),
					'htmlOptions'=>array('class'=>'miniTable parentTable'),
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
					)
				)); ?>

			</div>

			<div id="authItemChildren">

				<div><?php echo Yii::t('AuthModule.core', 'Children'); ?></div>

				<?php $this->widget('zii.widgets.grid.CGridView', array(
					'dataProvider'=>$childDataProvider,
					'template'=>'{items}',
					'emptyText'=>Yii::t('AuthModule.core', 'This item has no children.'),
					'htmlOptions'=>array('class'=>'miniTable parentTable'),
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
    						'name'=>'remove',
    						'header'=>'&nbsp;',
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'removeColumn'),
    						'value'=>'$data->removeChildColumn()',
    					),
					)
				)); ?>

			</div>

			<?php if( $childForm!==null ): ?>

				<div id="authItemAddChild">

					<h4><?php echo Yii::t('AuthModule.core', 'Add Child'); ?></h4>

					<div class="form">

						<?php echo $childForm->render(); ?>

					</div>

				</div>

			<?php endif; ?>

		<?php else: ?>

			<p class="info">

				<?php echo Yii::t('AuthModule.core', 'No relations need to be set for the superuser role.'); ?><br />
				<?php echo Yii::t('AuthModule.core', 'Super users are always granted access implicitly.'); ?>

			</p>

		<?php endif; ?>

	</div>

</div>
