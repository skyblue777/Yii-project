<table class="items">
    <tr class="<?php echo $class;?>">
        <td align="center" class="checkbox-column"><?php echo CHtml::checkBox('item', false, array('value'=>$model->id));?></td>
        <td class="sort-handle"><?php echo CHtml::encode($model->title);?></td>
        <td width="50" align="center" class="price-required-column"><?php if ($model->parent_id!=AdsSettings::ADS_ROOT_CATEGORY) echo '<a href="'.baseUrl().'/?r=Core/service/ajax&SID=Core.Category.changePriceRequired&id='.$model->id.'&value='.($model->price_required ? 0 : 1).'" class="'.($model->price_required ? 'active' : '').'"></a>'; ?></td>
        <td width="50" align="center" class="paid-ad-column"><?php echo '<a href="'.baseUrl().'/?r=Core/service/ajax&SID=Core.Category.changePaidAdRequired&id='.$model->id.'&value='.($model->paid_ad_required ? 0 : 1).'" class="'.($model->paid_ad_required ? 'active' : '').'"></a>'; ?></td>
        <td width="100" align="center" class="actions-column">
            <?php //echo CHtml::link('View', '#');?>
            <?php echo CHtml::link(Language::t(Yii::app()->language,'Backend.Common.Common','Edit'), array('update', 'id'=>$model->id));?>
            <?php echo CHtml::link(Language::t(Yii::app()->language,'Backend.Common.Common','Delete'), array('/Core/service/ajax', 'SID'=>'Core.Category.delete', 'ids[]'=>$model->id), array('class'=>'delete'));?>
        </td>
    </tr>
</table>