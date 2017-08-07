<style type="text/css">
.row input.buttons { margin-left: 0px; }
</style>

<table cellpadding="2" cellspacing="5" border="0">
<tbody>
    <?php foreach($colors as $color) : ?>
        <tr>
            <td>
                <?php
                $selected = FALSE;
                if ($color == $this->value) $selected = TRUE;
                echo CHtml::radioButton($this->name,$selected,array('value'=>$color,'uncheckValue'=>NULL,'id'=>'color-'.$color)); ?>
            </td>
            <td><div class="btn-demo-search btn-demo-search-<?php echo $color; ?>"></div></td>
            <td><div class="btn-demo-submit btn-demo-submit-<?php echo $color; ?>"></div></td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>