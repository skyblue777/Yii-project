<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
);
if (\$model->id)
    \$this->breadcrumbs = CMap::mergeArray(\$this->breadcrumbs, array(\$model->{$nameColumn}=>array('view','id'=>\$model->{$this->tableSchema->primaryKey}),'Update'));
else
    \$this->breadcrumbs = CMap::mergeArray(\$this->breadcrumbs, array('Create'));\n";
?>

$this->menu=array(
);
?>

<h1><?php echo "<?php echo \$model->{$this->tableSchema->primaryKey} ? 'Update' : 'Create';?> {$this->modelClass} <?php echo \$model->{$this->tableSchema->primaryKey}; ?>";?></h1>

<?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>