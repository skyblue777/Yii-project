<?php 
$asset = Yii::app()->core->AssetUrl.'/elfinder-1.1';
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery.ui');
$cs->registerScriptFile($asset.'/js/elfinder.min.js', CClientScript::POS_BEGIN);
$cs->registerCssFile($asset.'/css/elfinder.css');
?>

<script type="text/javascript" charset="utf-8">
$().ready(function() {
    var f = $('#elfinder').elfinder({
        url : '<?php echo $this->createUrl('/Core/service/command', array('SID'=>'Core.FileManager.cmdConnect')); ?>',
        lang : 'en',
        docked : true,
        height: 490
    })
});
</script>

<div id="elfinder"></div>

