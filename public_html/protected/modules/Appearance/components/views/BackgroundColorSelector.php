<table cellpadding="2" cellspacing="5" border="0">
<tbody>
    <?php foreach($colors as $color) : ?>
        <tr>
            <td>
                <?php
                $selected = FALSE;
                if ($color == $this->value) $selected = TRUE;
                echo CHtml::radioButton($this->name,$selected,array('value'=>$color,'uncheckValue'=>NULL,'id'=>'back-color-'.$color)); ?>
            </td>
            <td><div class="bar-demo-<?php echo $color; ?>"></div></td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>