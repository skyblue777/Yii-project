<?php
$css = <<<EOD
    .controllerRow{background: #DDD; font-weight: bolder;}
    .nameColumn{text-align:left}
    .pathColumn{text-align:right}
EOD;
Yii::app()->clientScript->registerCss('action-list-css',$css);
?>
<?php foreach( $items as $key=>$item ): ?>

	<?php if( isset($item['actions'])===true && $item['actions']!==array() ): ?>

		<?php 
        $controllerKey = isset($moduleName)===true ? ucfirst($moduleName).'.'.$key : $key;
        $controllerID = str_replace('.','_',$controllerKey);
		$controllerExists = isset($existingItems[ $controllerKey.'.*' ]); 
        ?>

		<tr class="controllerRow <?php echo $controllerExists===true ? 'exists' : ''; ?>">
			<td class="checkboxColumn">
                <a href="#" class="controller-handler" id="<?php echo $controllerID ; ?>">[ + ]</a>
            </td>
            <td class="nameColumn"><?php 
            echo FHtml::link(ucfirst($key).'Controller', array('/Core/auth/default/permissions', 'controller' => $controllerKey, 'type' => FAuthManager::ACTION_ITEM_TYPE)); 
            ?></td>
			<td class="pathColumn"><?php //echo substr($item['path'], $basePathLength+1); ?></td>
		</tr>

		<?php $i=0; foreach( $item['actions'] as $action ): ?>

			<?php $actionKey = $controllerKey.'.'.$action['name']; ?>
			<?php $actionExists = isset($existingItems[ $actionKey ]); ?>

			<tr class="actionRow <?php echo 'action'.$controllerID,' ', $actionExists===true ? 'exists' : ''; ?> <?php echo ($i++ % 2)===0 ? 'odd' : 'even'; ?>">
				<td class="checkboxColumn"><?php echo $actionExists===false ? $form->checkBox($model, 'items['.$actionKey.']') : ''; ?></td>
				<td class="nameColumn"><?php echo $action['name']; ?></td>
				<td class="pathColumn"><?php echo substr($item['path'], $basePathLength+1).'('.$action['line'].')'; ?></td>
			</tr>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php if( $key==='modules' && $items['modules']!==array() ): ?>

		<?php if( $showModuleHeadingRow===true ): ?>

			<tr><th class="moduleHeadingRow" colspan="3"><?php echo Yii::t('AuthModule.core', 'Modules'); ?></th></tr>

		<?php endif; ?>

		<?php foreach( $item as $moduleName=>$c ): ?>

			<tr><th class="moduleRow" colspan="3"><?php echo ucfirst($moduleName).'Module'; ?></th></tr>

			<?php $this->renderPartial('_generateItems', array(
				'model'=>$model,
				'form'=>$form,
				'items'=>$c,
				'existingItems'=>$existingItems,
				'moduleName'=>$moduleName,
				'showModuleHeadingRow'=>false,
				'basePathLength'=>$basePathLength,
			)); ?>

		<?php endforeach; ?>

	<?php endif; ?>

<?php endforeach; ?>
<?php
$script = <<<EOD
$('.controller-handler').click(function(){
    if( $(this).html() == '[ - ]'){
        $('.action'+$(this).attr('id')).hide();
        $(this).html('[ + ]');
    } else {
        $('.action'+$(this).attr('id')).show();
        $(this).html('[ - ]');
    }
    return false;
});

$('.actionRow').hide();
EOD;
Yii::app()->clientScript->registerScript('click', $script, CClientScript::POS_READY);
?>