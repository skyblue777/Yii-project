<?php
$this->breadcrumbs=array(
    'Setting Params',
);
?>

<h1><?php echo Language::t(Yii::app()->language,'Backend.Common.Menu','Settings')?></h1>

<?php 
if (count($modules)):
// standard Setting form is used
?>
<div class="form wide">
    <form id="FilterForm" method="post">
        <div class="row">
            <?php echo CHtml::label('Edit settings for', 'module');?>
            <?php echo CHtml::dropDownList('SettingParam[module]', $module, $modules, array('prompt'=>'---Select module---', 'onchange'=>"\$('#FilterForm').submit();"));?>
        </div>
    </form>
</div>
<!-- search-form -->
<?php 
else:
// custom setting form is used
?>
    <?php $module = '';?>
<?php endif;?>

<?php
$form = $this->beginWidget('Admin.components.ParamForm',array(
    'params' => $params,
    'config' => $config
));
    $form->beginForm();
    echo CHtml::hiddenField('SettingParam[module]', $module);
    $group = '';
    foreach($form->elements as $name => $elm) {
        if ($group != $elm->param->setting_group) {
            $group = $elm->param->setting_group;
            echo "<div class=\"form-group-heading\">".Language::t(Yii::app()->language,'Backend.Admin.Setting-Group',$elm->param->setting_group)."</div>";
        }
        echo $elm->render();
    }
    $form->endForm();
$this->endWidget();
?>