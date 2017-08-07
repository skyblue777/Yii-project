<?php
// change the following paths if necessary
$yii = dirname(__FILE__) . '/yii-1.1.7.r3135/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
// remove the following line when in production mode
defined('YII_DEBUG') or define ('YII_DEBUG', TRUE);
if (YII_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
//Installation bootstrap
if (!is_writable(dirname(__FILE__) . '/assets') ||
    !is_writable(dirname(__FILE__) . '/protected/runtime') ||
    (isset($_GET['r']) && $_GET['r'] == 'install/default') ||
    (isset($_GET['r']) && $_GET['r'] == 'install/default/index') ||
    (!file_exists(dirname(__FILE__) . '/protected/config/environment.php') && !isset($_GET['r'])) ||
    (!file_exists(dirname(__FILE__) . '/protected/config/environment.php') && isset($_GET['r']) && strpos($_GET['r'], 'install/default') !== 0)
) {
    require_once (dirname(__FILE__) . '/protected/modules/install/install.php');
} else {
    require_once ($yii);
    //Set language
    $app = Yii::createWebApplication($config);

    if (file_exists(dirname(__FILE__) . '/protected/config/environment.php'))
        if (Yii::app()->db->createCommand("SHOW TABLES LIKE 'setting'")->query()->rowCount > 0) {
            $language_params = SettingParam::model()->find('name=:name', array(':name' => 'LANG'));
            if (!is_null($language_params)) {
                $current_language = $language_params->value;
                $list_language_items = Language::model()->findAll('lang=:lang', array(':lang' => $current_language));
                if (sizeof($list_language_items) > 0)
                    Yii::app()->language = $current_language;
                else {
                    $setting = SettingParam::model()->find("name = 'LANG'");
                    $setting->value = Language::DEFAULT_LANGUAGE;
                    $setting->save();
                    Yii::app()->language = Language::DEFAULT_LANGUAGE;
                }
            }
        }

    $app->run();
}