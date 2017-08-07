<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$config = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
  
  // modules
    'modules'=>array(
        'Core' => array (
            //Sub modules
            'modules' => array(
                'auth'
            ),
        ),
        'Admin' => array(
        ),
         'User' => array(
        ),
        'Ads' => array(
        ),
    ),
  
  'import'=>array(
        'application.models.*',
        'application.components.*',
        'Core.components.*',
        'application.runtime.cache.*',
  ),
  
  // application components
  'components'=>array(
    'core'=>array(
        'class' => 'Core.extensions.FlexiCore',
        'FrontendRenderer' => false,
    ),
    
    'log'=>array(
        'class'=>'CLogRouter',
        'routes'=>array(
            array(
                'class'=>'CFileLogRoute',
                'levels'=>'error, warning, trace',
            ),
        ),
    ),
    'cache'=>array(
        'class'=>'system.caching.CDbCache',
        'autoCreateCacheTable'=>true,
        'connectionID'=>'db',
        'cacheTableName'=>'yii_cache',
    ),
    'mail' => array(
        'class' => 'application.modules.Core.extensions.vendors.mail.YiiMail',
        'transportType' => 'smtp',
        'transportOptions' => array(
            'host'=>'mail.sellmy.horse',
            'username'=>'info@sellmy.horse',
            'password'=>'c44cg6xxX!',
            'port'=>25,
           // 'encryption'=>'ssl',
        ),
        'logging' => true,
        'dryRun' => false
    ),
  ),
);

if (file_exists(dirname(__FILE__).'/environment.php')) {
    require 'environment.php';
    $config['components']['db'] = array(
            'connectionString' => DB_CONNECTION,
            'emulatePrepare' => true,
            'enableParamLogging' => true,
            'username' => DB_USER,
            'password' => DB_PWD,
            'charset' => 'utf8',
        );
}

return $config;