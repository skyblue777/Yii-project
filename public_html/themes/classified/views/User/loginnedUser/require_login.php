<?php
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.User.Login','login');
?>

<div class="mesg"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','You must')?> <a href="<?php echo $this->createUrl('/site/login'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','login')?></a> <?php echo Language::t(Yii::app()->language,'Frontend.User.Login','to access this page')?></div>