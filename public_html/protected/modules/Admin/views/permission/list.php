<h3>Pages under permission control</h3>

<?php if (count($pages)) :?>
    <ul>
    <?php foreach($pages as $name => $page): ?>
        <li style="padding-left:<?php echo $page['level'].'em';?>;">
            <?php 
            echo FHtml::link($page['description'] . ' ('.$name.')',
                            $this->createUrl('/Core/permission/editPage', array('name' => $name))); 
            ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
<?php endif; ?>