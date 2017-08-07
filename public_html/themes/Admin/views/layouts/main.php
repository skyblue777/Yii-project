<?php
Yii::app()->clientScript->registerCoreScript('jquery');
$script = "
jQuery('.breadcrumb a:first').addClass('Home');
jQuery('.breadcrumb span').prev('a').addClass('second-last');
";
Yii::app()->clientScript->registerScript('FixBreadcrumb', $script, CClientScript::POS_READY);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>
            <?php echo Settings::SITE_NAME; ?>
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex, nofollow" />
        <style type="text/css">
        /*<![CDATA[*/
                @import url("<?php echo Yii::app()->theme->baseUrl?>/styles/styles.css");
                @import url('<?php echo Yii::app()->theme->baseUrl?>/styles/general.css');
                @import url('<?php echo Yii::app()->theme->baseUrl?>/styles/tabmenu.css');
                
        /*]]>*/
        </style>
        <link rel="SHORTCUT ICON" href="<?php echo Yii::app()->theme->baseUrl?>/images/favicon.ico" /><!--[if IE]>
        <style type="text/css">
                @import url("<?php echo Yii::app()->theme->baseUrl?>/styles/ie.css");
        </style>
        <![endif]-->

        <?php Yii::app()->ClientScript->registerCoreScript('jquery'); ?>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl?>/scripts/menudrop.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl?>/scripts/common.js"></script>
    </head>
    <body>
        <div id="ajax-loading">
            <img src="<?php echo Yii::app()->theme->baseUrl?>/images/ajax-loader.gif" />&nbsp; Loading... Please wait...
        </div>
        <div class="Header">
            <div class="logo" style="margin-bottom: 10px;">
                <?php /*echo CHtml::link(CHtml::image(Yii::app()->theme->baseUrl.'/images/logo-flexica.gif', 'FlexiCORE Administration'), 
                    array('/Admin'), 
                    array('title'=>'FlexiCORE Administration')
                );*/?>
				<h1><?php echo CHtml::link(Settings::SITE_NAME.' Admin Panel',array('/Admin'));?></h1>
            </div>
            <div class="text-links">
                <div class="menu-text">
                    <a href="<?php echo baseUrl(); ?>" class="menu-text last" target="_blank"><?php echo Language::t(Yii::app()->language,'Backend.Common.Views','View Site')?></a>
                    |<a href="<?php echo $this->createUrl('/Core/service/command', array('SID'=>'Core.account.cmdLogout', 'returnUrl'=> baseUrl().Yii::app()->createUrl('/Admin'))); ?>" class="menu-text last"><?php echo Language::t(Yii::app()->language,'Backend.Common.Views','Logout')?></a>
                </div>
            </div>
            <div class="logged-in-as">
                <strong><?php echo Language::t(Yii::app()->language,'Backend.Common.Views','Logged in as:')?></strong> <?php echo Yii::app()->user->getState('username');?>
            </div>
        </div>
        <div class="menu-bar">
            <div id="menu">
                <?php $this->widget('Admin.components.ModuleMenus'); ?>
            </div>
        </div>
        
        <div class="content-container">
            <div class="body-container">
                <?php
                $this->widget('MessageBox');    
                echo $content
                ?>
            </div>
        </div>
        <div class="page-footer" style="text-align: center;"></div>
    </body>
</html>