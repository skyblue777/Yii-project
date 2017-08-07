<h3>Page access</h3>

<?php             
echo XHtml::beginForm();
?>
<table class="view">
    <thead>
        <th>Page</th>
        <?php
        $roleColWidth = 600 / count($roles);
        foreach($roles as $role)
        {
            if ($role == 'administrators') continue; 
            echo '<th style="text-align:center;width:'.$roleColWidth.'px">'.$role.'</th>';
        }
        ?>
    </thead>
    <?php foreach($pages as $name => $page) :?>
        <tr><td style="text-align:left;padding-left:<?php echo $page['level']?>em"><?php echo $page['description']; ?></td>
        <?php
        foreach($roles as $role)
        {
            if ($role == 'administrators') continue; 
            $granted = Yii::app()->authManager->hasItemChild($role, $name);
            echo '<td>'.XHtml::checkBox("{$role}[{$name}]", $granted, array('class' => 'PermissionCheckbox', 'value' => 1)).'</td>';
        }
        ?>
        </tr>
    <?php endforeach; ?>
</table>
<div class="Action">
    <?php echo XHtml::submitButton('Save', array('id' => 'savePermissions')); ?>
</div>
<?php 
echo XHtml::endForm();
?>

<script>
$(function(){
    $('#savePermissions').click(function(){
        $('.PermissionCheckbox').each(function(){
            if (this.checked == false) {
                this.checked = true;
                this.value = -1;
            }
        });
    });
});
</script>