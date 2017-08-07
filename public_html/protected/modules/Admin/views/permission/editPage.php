<h3>Page Permission</h3>

<?php echo FHtml::beginForm() ?>
<h3>Edit page</h3>
    <div class="Input">
        <label>Route</label>
        <?php echo FHtml::textField('AuthItem[name]', $page->name); ?>
    </div>
    <div class="Input">
        <label>Description</label>
        <?php echo FHtml::textField('AuthItem[description]', $page->description); ?>
    </div>
    <div class="Input">
        <label>Parent</label>
        <select name="AuthItem[parent]">
            <option value="">Top level page</option>
        <?php 
        $tree = $this->getPageTree();
        foreach ($tree as $name => $item) {
            $selected = '';
            if (in_array($page->name, $item['children']))
                $selected = 'selected="selected"';
            echo "<option value=\"{$name}\" style=\"padding-left:{$item['level']}em;\" {$selected}>{$item['description']}</option>";
        }
        ?>
        </select>
    </div>
    <div class="Action">
        <?php echo FHtml::submitButton('Save'); ?>
        <?php echo FHtml::link('Cancel', $this->createUrl('/Core/permission/listPages')); ?>
        <?php echo FHtml::link('Delete', $this->createUrl('/Core/permission/deltePage', array('name' => $name))) ?>
    </div>
<?php 
echo FHtml::endForm() ;
//Register script
$cs = Yii::app()->ClientScript;
$cs->registerScriptFile(Yii::app()->theme->BaseUrl.'/scripts/common.js', CClientScript::POS_BEGIN);
?>

