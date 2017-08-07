<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii-1.1.7.r3135/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/console.php';

// remove the following line when in production mode
 defined('YII_DEBUG') or define('YII_DEBUG',true);
 if (YII_DEBUG) {
     error_reporting(E_ALL);
     ini_set('display_errors', 1);
 }

require_once($yii);
Yii::createConsoleApplication($config)->run();
