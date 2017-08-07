    <select class="type1" name="<?php echo $this->inputName ?>" id="<?php echo $this->inputId; ?>" <?php echo $multiple; ?>>
    	<?php if (!empty($promptText)) : ?>
        	<option value="0">----<?php echo $promptText; ?>----</option>
        <?php endif; ?>
        <?php if(!empty($categories)): ?>
            <?php foreach($categories as $id => $cat): ?>
                <?php
                	if(!$this->showRootCategory && $this->rootCategoryId == $id) continue;
                	$selected = '';
                	if($this->valueField=='id' && is_array($selectedItems) && !empty($selectedItems) && in_array($id, $selectedItems))
                    	$selected = 'selected="selected"';
                    if($this->valueField=='alias' && is_array($selectedItems) && !empty($selectedItems) && in_array($cat['alias'], $selectedItems))
                    	$selected = 'selected="selected"';
                ?>
                <option value="<?php if($this->valueField=='id') echo $id; else echo $cat['alias']; ?>" <?php echo $selected ?> >
                    <?php echo  str_repeat('&nbsp;',$cat['level']*5) . urldecode($cat['title']); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>