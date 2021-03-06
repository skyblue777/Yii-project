<h3><?php echo Yii::t('AuthModule.core', 'Permissions'); ?></h3>

<?php if( count($items)>0 ): ?>

	<table class="permissionTable view" border="0" cellpadding="0" cellspacing="0">

		<thead>

			<tr>

				<th class="descriptionColumnHeading" style="width:25%;"><?php echo Yii::t('AuthModule.core', 'Permission'); ?></th>

				<?php foreach( $roles as $roleName=>$role ): ?>

					<th class="roleColumnHeading" style="width:<?php echo $roleColumnWidth; ?>%"><?php echo CHtml::encode($roleName); ?></th>

				<?php endforeach; ?>

			</tr>

		</thead>

		<tbody>

			<?php $i=0; foreach( $items as $name => $item ): ?>

				<tr class="<?php echo ($i++ % 2)===0 ? 'odd' : 'even'; ?>">

					<td><?php echo $item->description!='' ? CHtml::encode($item->description) : CHtml::encode($name); ?></td>

					<?php foreach( $roles as $roleName=>$role ): ?>

						<td>

							<?php if( $rights[ $roleName ][ $name ]===Rights::PERM_DIRECT ): ?>

								<?php echo CHtml::link(Yii::t('AuthModule.core', 'Revoke'), '#', array(
									'onclick'=>'jQuery.ajax({ type:"POST", url:"'.$this->createUrl('authItem/revoke', array('name'=>$role->name, 'child'=>$name)).'", data:{ ajax:true }, success:function() { $("#rightsPermissions").load("'.$this->createUrl('default/permissions',array('type'=>FAuthManager::ACTION_ITEM_TYPE,'controller'=>$this->get('controller'))).'", { ajax:true }); } }); return false;',
									'class'=>'revokeLink',
								));	?>

							<?php elseif( $rights[ $roleName ][ $name ]===Rights::PERM_INHERITED ): ?>

								<span title="<?php echo isset($parents[ $roleName ][ $name ])===true ? $parents[ $roleName ][ $name ] : ''; ?>">
									<?php echo Yii::t('AuthModule.core', 'Inherited'); ?> *
								</span>

							<?php else: ?>

								<?php echo CHtml::link(Yii::t('AuthModule.core', 'Assign'), '#', array(
									'onclick'=>'jQuery.ajax({ type:"POST", url:"'.$this->createUrl('authItem/assign', array('name'=>$role->name, 'child'=>$name)).'", data:{ ajax:true }, success:function() { $("#rightsPermissions").load("'.$this->createUrl('default/permissions', array('type'=>FAuthManager::ACTION_ITEM_TYPE,'controller'=>$this->get('controller'))).'", { ajax:true }); } }); return false;',
									'class'=>'assignLink',
								)); ?>

							<?php endif; ?>

						</td>

					<?php endforeach; ?>

				</tr>

			<?php endforeach; ?>

		</tbody>

	</table>

	<p class="info">*) <?php echo Yii::t('AuthModule.core', 'Hover to see from where the permission is inherited.'); ?></p>

	<script type="text/javascript">

		jQuery('.inheritedItem').rightsTooltip({
			title:'<?php echo Yii::t('AuthModule.core', 'Parents'); ?>: '
		});

	</script>

<?php else: ?>

	<p><?php echo Yii::t('AuthModule.core', 'No authorization items found.'); ?></p>

<?php endif; ?>
