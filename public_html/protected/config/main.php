<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$config = array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Titan Classifieds',

    // modules
    'modules'=>array(
        'Core' => array (
            //Sub modules
            'modules' => array(
                'auth'
            ),
        ),
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'generatorPaths'=>array('Core.extensions.gii.generators'),
            'password'=>'flexicore',
        ),
        'Admin' => array(
        ),
         'User' => array(
        ),
        // -------- begin listing site modules below ---------- //
        'Article' => array(
        ),
        'Ads' => array(
        ),
        'Money' => array(
        ),
        'Map' => array(
        ),
        'Messaging' => array(
        ),
        'Appearance' => array(
        ),
        'Language' => array(
        ),
        'install' => array(
        ),
    ),

    // preloading 'log' component
    'preload'=>array('log','core'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'Core.components.*',
    	'Language.models.Language'
    ),
    
    'defaultController'=>'site',

    // application components
    'components'=>array(
        'core'=>array(
            'class' => 'Core.extensions.FlexiCore',
            'FrontendRenderer' => false,
        ),
        'user'=>array(
            'allowAutoLogin'=>true,
        ),
        'authManager' => array(
            'class' => 'FAuthManager',
        ),
        'errorHandler'=>array(
            'class' => 'Core.extensions.base.FErrorHandler',
             //use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),

        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'urlSuffix'=>'.htm',
            'rules'=>array(
                // for site
                '<location>'=>'site/index',
                'site/reset-your-password'=>'site/forgotPassword',
                'site/support/<alias>'=>'site/support',
                'site/help/<alias>'=>'site/faqs',
                'site/help'=>'site/faqs',
                'site/admin'=>'Admin/default/index',
                'admin/login'=>'Admin/default/login',
                'admin/forgot-password'=>'Admin/default/forgotPassword',
                'admin/reset-password'=>'Admin/default/resetPassword',
                'admin/reset-confirm'=>'Admin/default/resetConfirm',
                // for log-inned user
                'my-acount/my-ads'=>'User/loginnedUser/viewMyAds',
                'my-acount/my-favorite-ads'=>'User/loginnedUser/viewMyFavoriteAds',
                'my-acount/my-profile'=>'User/loginnedUser/myProfile',
                // for listing ads (location)
                '<location>/list-by-category/<alias>-<cat_id:\d+>'=>'Ads/ad/listByCategory',
                'list-by-category/<alias>-<cat_id:\d+>'=>'Ads/ad/listByCategory',
                '<location>/list-by-location'=>'Ads/ad/listByArea',
                'list/all-ads'=>'Ads/ad/listByArea',
                '<location>/list-by-search/<alias>-<cat_id:\d+>'=>'Ads/ad/listBySearch',
                'list-by-search/<alias>-<cat_id:\d+>'=>'Ads/ad/listBySearch',
                'list/by-advanced-search'=>'Ads/ad/advancedSearch',
                // for view ad (location)
                '<area>/view-ad/<alias>-<id:\d+>'=>'Ads/ad/viewDetails',
                'view-ad/<alias>-<id:\d+>'=>'Ads/ad/viewDetails',
                'print-ad/<alias>-<id:\d+>'=>'Ads/ad/viewDetailsAsPrint',
                'share-ad-with-friend/<alias>-<id:\d+>'=>'Ads/ad/emailAdToFriend',
                'reply-ad/<alias>-<id:\d+>'=>'Ads/ad/replyToAd',
                // for editing ad
                'edit-ad/select-a-category'=>'Ads/ad/selectCategory',
                'edit-ad/pay-for-category/<alias>-<cat_id:\d+>'=>'Ads/ad/requirePaymentForPaidCategory',
                'edit-ad/create-your-ad/<alias>-<cat_id:\d+>'=>'Ads/ad/create',
                'edit-ad/update-your-ad/<alias>-<id:\d+>'=>'Ads/ad/update',
                'edit-ad/edit-your-ad'=>'Ads/ad/editUnsavedAd',
                'edit-ad/preview-your-ad'=>'Ads/ad/preview',
                'edit-ad/posting-message'=>'Ads/ad/performSaveAd',
                'edit-ad/promote-your-ad/<promotion>'=>'Ads/ad/performPromotion',
                'edit-ad/activating-message/<code>-<id:\d+>'=>'Ads/ad/activate',
                'edit-ad/deleting-message/<codes>-<ids:\d+>'=>'Ads/ad/delete',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
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

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
//    'params'=>require(dirname(__FILE__).'/params.php'),
);

if (file_exists(dirname(__FILE__).'/environment.php')) {
    require 'environment.php';
    $config['components']['db'] = array(
            'connectionString' => DB_CONNECTION,
            'emulatePrepare' => true,
            'enableParamLogging' => true,
            'username' => DB_USER,
            'password' => DB_PWD,
//            'charset' => 'utf8',
        );
} else {
    $config['preload'] = array('log');
    $config['components']['cache'] = null;
    $config['components']['urlManager']['urlFormat'] = 'get';
}


return $config;