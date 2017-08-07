<?php
$title = 'Email';
$tplName = $this->get('template','');
if ($tplName=='activation_email')
    $title = Language::t(Yii::app()->language,'Backend.Message.MailTemplate','Activation Email');
elseif ($tplName=='expriation_email')
    $title = Language::t(Yii::app()->language,'Backend.Message.MailTemplate','Expiration Email');
elseif ($tplName=='registration_email')
    $title = Language::t(Yii::app()->language,'Backend.Message.MailTemplate','Registration Email');
$this->breadcrumbs=array(
    $title
);
?>

<h1><?php echo $title; ?></h1>
<div class="form wide">
<?php echo CHtml::beginForm();?>
    <div class="row">
        <?php 
        $content = str_replace('<?php echo $','{',$content);
        $content = str_replace(array('; ?>',';?>'),'}',$content);
        $this->widget('Core.components.tinymce.ETinyMce', array(
            'name'=>'content', 
            'value'=>$content, 
            'editorTemplate'=>'full',
            'options'=>array(
                'theme_advanced_buttons1'=>'cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,removeformat,cleanup,|,spellchecker,|,visualaid,visualchars,|,ltr,rtl,|,code,preview,fullscreen',
                'remove_script_host'=>true,
                'relative_urls'=>true,
            ),
            'width'=>'500px',
            'height'=>'400px',
            'useCompression'=>false,
            'useElFinder'=>false,
        )); ?>

        <?php echo CHtml::hiddenField('file', $selectedFile); ?>
    </div>
    
    <div>
        <?php echo CHtml::submitButton(Language::t(Yii::app()->language,'Backend.Common.Common','Save')); ?>
    </div>
<?php echo CHtml::endForm();?>
</div>