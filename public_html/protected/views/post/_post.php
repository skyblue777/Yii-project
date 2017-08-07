<h2><?php echo CHtml::encode($data->title);?></h2>

<div class="post-leading">
    <?php echo $data->leading_text ?>
</div>

<?php if (isset($show_content) && $show_content == true) :?>
<div class="post-content">
    <?php echo $data->content ?>
</div>
<?php endif;?>

<div class="post-footer">
    Tags: <?php echo $data->tags; ?>
    <?php if (! isset($show_content) || $show_content == false) :?>
        <?php echo CHtml::link('read more', url('view', array('id' => $data->id))); ?>
    <?php endif;?>
</div>
