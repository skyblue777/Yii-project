<?php
/**
 * DefaultController class
 *
 * @category  KnowledgeBase
 * @package   Install
 * @author    Minh Le <minh.le@webflexica.com>
 * @copyright 2009 Webflexica
 * @license   http://www.webflexica.com/license/ license
 * @version   Release: @package_version@
 * @link      http://knowledgebase.webflexica.com/
 */
class DefaultController extends CController
{
    public $structureFile = 'setup.sql';
    public $initialDataFile = 'init.sql';
    public $dataFile = 'data.sql';

    protected $connection = null;

    public function init()
    {
        Yii::app()->Theme = 'install';
        Yii::app()->layout = 'main';
    }

    /**
     * welcome page
     *
     * @return void
     */
    public function actionIndex()
    {
        //check permit
        $assetsPermit = false;
        $uploadPermit = false;
        $runtimePermit = false;
        $cachedPermit = false;

        $basePath = Yii::app()->basePath;

        clearstatcache();
        if (is_writable($basePath . '/../assets') === true) {
            $assetsPermit = true;
        }
        if (is_writable($basePath . '/../uploads') === true) {
            $uploadPermit = true;
        }
        if (is_writable($basePath . '/runtime') === true) {
            $runtimePermit = true;
        }

        if (is_writable($basePath . '/runtime/cache') === true) {
            $cachedPermit = true;
        }
        $this->render('welcome', array(
                'assetsPermit' => $assetsPermit,
                'uploadPermit' => $uploadPermit,
                'runtimePermit' => $runtimePermit,
                'cachedPermit' => $cachedPermit,
            )
        );
    }

    /**
     * index page
     * check folders permit 777
     * input database information
     * create ./protected/config/environment.php file
     *
     * @return void
     */
    public function actionEnvironment()
    {
        Yii::app()->session->remove('config');
        $model = new ConfigForm();
        if (isset($_POST['ConfigForm']) === true) {
            $model->attributes = $_POST['ConfigForm'];
            $model->password = $_POST['ConfigForm']['password'];
            if ($model->validate() === true) {
                if ($model->checkConnection() === true) {
                    //create enviroment file
                    $configPath = Yii::getPathOfAlias('application.config.*');
                    $envSampleFile = $configPath . DIRECTORY_SEPARATOR . 'environment-sample.php';
                    if (file_exists($envSampleFile) === false) {
                        throw new CHttpException(500, 'Not found "' . $envSampleFile . '" file');
                    }

                    $content = file_get_contents($envSampleFile);
                    $searches = array('@base_url@', '@host@', '@port@', '@dbname@',
                        '@username@', '@password@');
                    $replaces = array($model->baseUrl, $model->host, $model->port, $model->dbName,
                        $model->username, $model->password);
                    $content = str_replace($searches, $replaces, $content);
                    if (is_writable($configPath) === true) {
                        file_put_contents($configPath . '/environment.php', $content);
                    } else {
                        Yii::app()->session['config'] = $content;
                    }
                    $this->redirect(array('default/language'));
                } else {
                    $this->redirect(array('default/ErrorConnection'));
                }
            }
        }

        $this->render('environment', array(
                'model' => $model,
            )
        );
    }

    public function actionLanguage()
    {

        if (Yii::app()->request->isPostRequest) {
            if (isset($_POST['language']) && $_POST['language'] != '')  {
                //echo Yii::app()->language;die;
                user()->setState('language',$_POST['language']);
                $this->redirect(array('default/build'));

            }

            //Yii::app()->language=$_POST['language'].'_'.strtoupper($_POST['language']);

        }

        /*list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterLanguageSettingsParams'));
        $controller->init();
        $controller->run($actionId);*/
        $this->render('language');
    }


    /**
     * build database structures,
     * optional: insert data example
     *
     * @return void
     */
    public function actionBuild()
    {
        //echo user()->getState('language');die;

//        try {
//            $resolveMsg = 'Click <a href="'.$this->createUrl('/install/default/environment').'">here</a> to resolve. You might one to read the information in next 2 steps carefully.';
        //create enviroment file
        $configPath = Yii::getPathOfAlias('application.config.*');
        $envFile = $configPath . DIRECTORY_SEPARATOR . 'environment.php';
        if (file_exists($envFile) === false) {
//                Yii::app()->user->setFlash('error', "File not found <strong>./protected/config/environment.php</strong> file. {$resolveMsg}");
        } else {
            Yii::app()->session->remove('config');
            include_once $envFile;
            $this->connection = new CDbConnection(DB_CONNECTION, DB_USER, DB_PWD);
            $this->connection->active = true;
            $canConnect = true;
        }
//        } catch (Exception $e){
//            Yii::app()->user->setFlash('error', "Can't connect to database. {$resolveMsg}");
//            $this->refresh();
//        }

        if (isset($_POST['install']) === true) {
            if (is_object($this->connection) === true) {
                //build structure
                $this->runSql($this->getSql($this->structureFile));
	            // initial data
                $this->runSql($this->getSql($this->initialDataFile));

	            // languages
	            $this->runSql($this->getSql('language.sql'));

                $categorySql = 'category_'.user()->getState('language','').'.sql';
                $sql = $this->getSql($categorySql);
                if ($sql !== false) $this->runSql($sql);

                //insert data
                if (isset($_POST['example']) === true) {
                    $sql = $this->getSql($this->dataFile);
                    $sqlArr = explode(');', $sql);
                    foreach ($sqlArr as $script) {
                        if (strripos($script, 'insert  into') !== false) {
                            $script .= ')';
                            $connection->createCommand($script)->execute();
                        }
                    }
                }
                try {
                    if (Yii::app()->db->createCommand("SHOW TABLES LIKE 'setting'")->query()->rowCount > 0) {

                        if (user()->hasState('language')) {
                            $setting = SettingParam::model()->find("name = 'LANG'");
                            $setting->value = user()->getState('language');
                            $setting->save();
                        }
                    }
                } catch (CDbException $e) {
                    die('Database is not installed completely.');
                }

	            // import file

	            $this->redirect(array('info'));
            }
        }

        $this->render('build', array('canConnect' => $canConnect));
    }

    /**
     * get schema file
     *
     * @param string $file schema filename
     *
     * @return string
     */
    protected function getSql($file)
    {
        $filePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filePath) === false) {
            Yii::log($file . ' is not found.');
            return false;
            //throw new CHttpException(500, "Not found sql file.");
        }

        $sql = file_get_contents($filePath);
        return $sql;
    }

    protected  function runSql($sql) {
        $sqlArr = explode(');', $sql);
        foreach ($sqlArr as $script) {
            $script = trim($script);
            if (empty($script))
                continue;
            else
                $script .= ');';
            $result = $this->connection->createCommand($script)->execute();
        }
    }

    /**
     * finish install applcation
     * redirect user to admin panel or home site.
     *
     * @return void
     */
    public function actionInfo()
    {
        $modules = array('', 'Ads', 'Appearance', 'Map', 'Messaging', 'Money', 'User');
        foreach ($modules as $module)
            FSM::run('Core.Settings.db2php', array('module' => $module));

        $model = new SettingInfoForm();
        if (isset($_POST['SettingInfoForm']) === true) {
            $model->attributes = $_POST['SettingInfoForm'];
            if ($model->validate() === true) {
                Yii::import('application.modules.Cms.models.Setting');
                //param SITE_NAME
                $siteNameSetting = SettingParam::model()->findByAttributes(array('name' => 'SITE_NAME'));
                if (is_null($siteNameSetting)) {
                    $siteNameSetting = new SettingParam();
                    $siteNameSetting->name = 'SITE_NAME';
                    $siteNameSetting->label = 'Title of the site';
                    $siteNameSetting->description = "";
                    $siteNameSetting->setting_group = '';
                    $siteNameSetting->ordering = 2;
                }
                $siteNameSetting->value = $model->siteName;
                if ($siteNameSetting->isNewRecord)
                    $siteNameSetting->save();
                else
                    $siteNameSetting->updateByPk($siteNameSetting->id, array('value' => $siteNameSetting->value));

                //param ADMIN_EMAIL
                $emailSetting = SettingParam::model()->findByAttributes(array('name' => 'ADMIN_EMAIL'));
                if (is_null($emailSetting)) {
                    $emailSetting = new SettingParam();
                    $emailSetting->name = 'ADMIN_EMAIL';
                    $emailSetting->label = "Administrator's email";
                    $emailSetting->description = '';
                    $emailSetting->setting_group = '1. General settings';
                    $emailSetting->ordering = 5;
                }
                $emailSetting->value = $model->adminEmail;
                if ($emailSetting->isNewRecord)
                    $emailSetting->save();
                else
                    $emailSetting->updateByPk($emailSetting->id, array('value' => $emailSetting->value));

                //param SITE_CONTACT
                $contactSetting = SettingParam::model()->findByAttributes(array('name' => 'SITE_CONTACT'));
                if (is_null($contactSetting)) {
                    $contactSetting = new SettingParam();
                    $contactSetting->name = 'SITE_CONTACT';
                    $contactSetting->label = "Contact Email";
                    $contactSetting->description = '';
                    $contactSetting->setting_group = '';
                    $contactSetting->ordering = 1;
                }
                $contactSetting->value = $model->adminEmail;
                if ($contactSetting->isNewRecord)
                    $contactSetting->save();
                else
                    $contactSetting->updateByPk($contactSetting->id, array('value' => $contactSetting->value));

                //param SITE_CONTACT
                $baseUrlSetting = SettingParam::model()->findByAttributes(array('name' => 'SITE_URL'));
                if (is_null($baseUrlSetting)) {
                    $baseUrlSetting = new SettingParam();
                    $baseUrlSetting->name = 'SITE_URL';
                    $baseUrlSetting->label = "Site Url";
                    $baseUrlSetting->description = '';
                    $baseUrlSetting->setting_group = '1. General settings';
                    $baseUrlSetting->ordering = '';
                }
                $baseUrlSetting->value = baseUrl();
                if ($baseUrlSetting->isNewRecord)
                    $baseUrlSetting->save();
                else
                    $baseUrlSetting->updateByPk($baseUrlSetting->id, array('value' => $baseUrlSetting->value));

                //create Settings.php
                FSM::run('Core.Settings.db2php', array('module' => ''));

                Yii::import('application.modules.User.models.User');
                $user = User::model()->findByPk(1);
                $user->username = 'admin';
                $user->email = $model->adminEmail;
                $password = substr(md5(uniqid(mt_rand(), true)), 0, 10);
                $user->password = md5($password);
                $user->validation_type = 1;
                $user->validation_expired = 1;
                $user->status = User::STATUS_ACTIVE;
                $user->first_name = 'Web';
                $user->last_name = 'Master';
                if ($user->save() === true) {
                    Yii::app()->user->setFlash('email', $user->email);
                    Yii::app()->user->setFlash('password', $password);
                    $this->redirect(array('finish'));
                }
                Yii::trace(CVarDumper::dumpAsString($user->getErrors()));
            }
        }
        $this->render('info', array('model' => $model));
    }



    protected  function actionImportFile($file) {
        Yii::import('Language.models.*');
        Yii::import('Language.extensions.vendors.PHPExcel',true);

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($file); //$file --> your filepath and filename
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
        for ($row = 2; $row <= $highestRow; ++$row) {
            $code=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
            $value=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
            $group=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
            $module=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
            $type=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
            $criteria = new CDbCriteria ();
            $criteria->compare ( 'lang', $model->lang );
            $criteria->compare ( '`group`', $group );
            $criteria->compare ( 'module', $module );
            $criteria->compare ( 'type', $type );
            $criteria->compare ( 'code', $code );
            $list = Language::model ()->findAll ( $criteria );
            if(sizeof($list)>0){
                foreach ($list as $item){
                    $item->value=$value;
                    $item->save();
                }
            }
            else {
                $item=new Language();
                $item->lang = $model->lang;
                $item->code = $code;
                $item->value = $value;
                $item->group = $group;
                $item->module = $module;
                $item->type = $type;
                $item->save();
            }
        }
        //Install default language for the site after installing.
        try {
            if (Yii::app()->db->createCommand("SHOW TABLES LIKE 'setting'")->query()->rowCount > 0) {

                if (user()->hasState('language')) {
                    $setting = SettingParam::model()->find("name = 'LANG'");
                    $setting->value = user()->getState('language');
                }
            }
        } catch (CDbException $e) {
            die('Database is not installed completely.');
        }
    }

    /**
     * alert invalid database information
     *
     * @return void
     */
    public function actionErrorConnection()
    {
        $this->render('errordb');
    }

    /**
     * finish install applcation
     * redirect user to admin panel or home site.
     *
     * @return void
     */
    public function actionFinish()
    {
        // remove all files in uploads folder if there is no ads
        Yii::import('Ads.models.Annonce');
        if (Annonce::model()->count() <= 0) {
            $uploadPath = Yii::app()->basePath . '/../uploads/ads/';
            $uploadTempPath = $uploadPath . 'temp/';
            foreach (glob($uploadPath . '*.*') as $v) {
                unlink($v);
            }
            foreach (glob($uploadTempPath . '*.*') as $v) {
                unlink($v);
            }
        }
        $this->render('finish');
    }

    /**
     * send environment.php file
     *
     * @return void
     */
    public function actionConfig()
    {
        if (Yii::app()->session->contains('config') === true) {
            $fileName = 'environment.php';
            $content = Yii::app()->session['config'];
            $mimeType = 'txt/php';
            Yii::app()->request->sendFile($fileName, $content, $mimeType);
        } else {
            throw new CHttpException(404);
        }
    }
}