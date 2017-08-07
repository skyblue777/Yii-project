<?php cs()->registerCoreScript('jquery'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="<?php echo $this->descriptionMetaTagContent; ?>" />
    <meta name="keywords" content="<?php echo $this->keyWordsMetaTagContent; ?>" />
    <title><?php echo $this->pageTitle; ?></title>
    <link href="<?php echo themeUrl()?>/styles/reset.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/<?php if (AppearanceSettings::BUTTON_COLOR!='') echo AppearanceSettings::BUTTON_COLOR; else echo 'green'; ?>.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/general.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/form.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/skin.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo themeUrl(); ?>/scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link href="<?php echo themeUrl()?>/scripts/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="container">
        <div id="main">
            <?php
            $this->widget('zii.widgets.CBreadcrumbs', array(
                'homeLink'=>'<a class="Home" href="'.baseUrl().'">'.Language::t(Yii::app()->language,'Frontend.Common.Common','Home').'</a>',
                'links'=>$this->breadcrumbs,
                'htmlOptions'=>array(
                    'id' => 'pageBreadCrumb',
                    'class' => 'link-top'
                ),
                'separator' => " &gt; "
            ));
            ?>
            <?php echo $content; ?>
        </div>
    </div>   
</body>
</html>