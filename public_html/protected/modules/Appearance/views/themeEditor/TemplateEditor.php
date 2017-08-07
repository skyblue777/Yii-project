<?php
/** @var CClientScript */
$cs = Yii::app()->clientScript;
$themeUrl = Yii::app()->themeManager->baseUrl;

$js = $themeUrl.'/global/scripts/CodeMirror/js/codemirror.js';
if ($cs->isScriptFileRegistered($js) === false) {
    $cs->registerScriptFile($js);
}
?>

<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Backend.Apperance.EditorTemplate','Edit Stylesheet'),
);
?>

<?php if (isset($_GET['file'])) echo '<h1>'.$_GET['file'].'</h1>'?>
<?php if (Yii::app()->user->hasFlash('error')):?>
<div><?php echo Yii::app()->user->getFlash('error');?></div>
<?php endif;?>
<table>
    <tr>
        <td width="800">
        <?php if ($currentFile !== null) :?>
        <?php echo CHtml::beginForm(array('/Appearance/themeEditor/TemplateEditor', 'file'=>$_GET['file']));?>
            <div style="border: 1px solid black; padding: 3px; background-color: #F8F8F8">
            <?php echo CHtml::textArea('code', $currentFile, array('cols'=>120, 'rows'=>30));?>
            </div>
            <div style="padding: 10px 0 0;">
            	<?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Common.Common','Update'), array('class' => 'btn'));?>
            </div>
        </form>
        <script type="text/javascript">
          var editor = CodeMirror.fromTextArea('<?php echo CHtml::getIdByName('code');?>', {
            height: "350px",
            parserfile: [<?php echo $parserFile;?>],
            stylesheet: ["<?php echo $themeUrl;?>/global/scripts/CodeMirror/css/xmlcolors.css",
                        "<?php echo $themeUrl;?>/global/scripts/CodeMirror/css/jscolors.css",
                        "<?php echo $themeUrl;?>/global/scripts/CodeMirror/css/csscolors.css",
                        "<?php echo $themeUrl;?>/global/scripts/CodeMirror/css/phpcolors.css"],
            path: "<?php echo $themeUrl;?>/global/scripts/CodeMirror/js/",
            continuousScanning: 500,
            lineNumbers: true
          });
        </script>
        <?php endif;?>
        </td>
        <td width="200" id="file-list" valign="top">
            <h3><?php echo Language::t(Yii::app()->language,'Backend.Appearance.EditorTemplate','Styles')?></h3>
            <ul>
            <?php foreach ($cssFiles as $name => $path):?>
                <li <?php if (isset($_GET['file']) && $name == $_GET['file']) echo 'class="active"'?>>
                <?php echo CHtml::link($name, url('/Appearance/themeEditor/templateEditor', array('file'=>$name)));?></li>
            <?php endforeach;?>
            </ul>
        </td>
    </tr>
</table>