<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div class="span-6 last">
		<div id="sidebar" class="crud-menu">
            <?php $this->widget('zii.widgets.CMenu',array(
                'items'=>$this->menu,
                'lastItemCssClass'=>"last"
            ));
            ?>
		</div><!-- sidebar -->
	</div>
</div>
<?php $this->endContent(); ?>