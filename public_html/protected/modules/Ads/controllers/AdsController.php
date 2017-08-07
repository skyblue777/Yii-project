<?php

class AdsController extends BackOfficeController
{
    public $categories = null;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $this->actionList();
	}

	/**
	 * Manages all models.
	 */
	public function actionList()
	{
        $model=new Annonce('search');
        $model->unsetAttributes();
        $model->public = '';
        //$countAllAds = 0;
        
        if(isset($_GET['Annonce']))
            $model->attributes=$_GET['Annonce'];
        
        /*if (!Yii::app()->request->isAjaxRequest)
        {
            $countAllAds = Annonce::model()->count();
        }*/
        
        // category
        /*if (($cache=Yii::app()->getComponent('cache'))!==null)
        {
            $key = 'Core.components.CategoryTree.categories#level:-1';
            if(($data=$cache->get($key))!==false)
                $this->categories = $data;
        }
        if (is_null($this->categories))
        {
            $this->categories = array();
            $criteria=new CDbCriteria;
            $criteria->compare('parent_id', 0);
            $criteria->order = 'ordering';
            $models = Category::model()->findAll($criteria);
            $this->findChildrenRecursive($models);
        }*/

        $this->render('list',array(
            'model'=>$model,
            //'countAllAds' => $countAllAds,
        ));
	}
    

    protected function findChildrenRecursive($models, $count=0)
    {
        foreach ($models as $index => $model)
        {            
            $this->categories[$model->id] = str_repeat('--', $count).$model->title;
            $children = $model->children;
            if (is_array($children) && count($children))
                $this->findChildrenRecursive($children, $count+1);
        }
    }
    
    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            if (($id = $this->get('id',null)) !== null) {
                $ids = is_numeric($id) ? array($id) : explode(',',$id);
                
                // delete one or multiple objects given the list of object IDs
                $result = FSM::run('Ads.Ads.delete', array('ids' => $ids));
                if ($result->hasErrors()) {
                    echo $result->getError('ErrorCode');
                } elseif(!Yii::app()->request->isAjaxRequest) {
                    // only redirect user to the admin page if it is not an AJAX request
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('list'));
                }
            } else {
                throw new CHttpException(400,Yii::t('Core','Cannot delete item with the given ID.'));
            }
        } else {
            throw new CHttpException(400,Yii::t('Core','Invalid request. Please do not repeat this request again.'));
        }
    }
    
    
    
    public function actionUpdate()
    {
        $id = $this->get('id','');
        $model = Annonce::model()->findByPk($id);
        if (is_null($model))
            throw new CHttpException(400,Yii::t('Ads.Ads','Sorry! This ad is not found'));
            
        if (Yii::app()->request->IsPostRequest)
        {
            // save posted data
            $_POST['validateOnly'] = ($this->post('ajax','') == 'ads-form');
            $result = FSM::run('Ads.Ads.save', $_POST);
            $model = $result->model; 

            if ($this->post('ajax','') == 'ads-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }   
            if (! $result->hasErrors()) {
                $this->message = Yii::t('Ads','Ads has been updated successfully.');
                $this->redirect(array('update', 'id'=>$model->id));
            }
        }
        
        $this->render('update',array('model'=>$model));    
    }
    
    public function actionUploadImage()
    {
        Yii::import("application.extensions.EAjaxUpload.qqFileUploader");
        $allowedExtensions = array('jpg','png','jpeg','gif','bmp');
        $sizeLimit = 1*1024*1024;
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

        $attachmentFolder = 'uploads/ads/temp/';
        //Create a folder tmp.userId to save file temp
        if (!is_dir(Yii::getPathOfAlias('application') . '/../' . $attachmentFolder)) {
            $r = mkdir(Yii::getPathOfAlias('application') . '/../' . $attachmentFolder, 0777, true);
            if (!$r) {
                throw new CHttpException(501, 'Could not create folder ' . $attachmentFolder);
            }
        }
        $result = $uploader->handleUpload($attachmentFolder);
        $result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result; // it's array                               
    }
    
    
    //***************** parameters *********************//
    public function actionSettings() {
        
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);
    }
    
    public function actionGetAds() {
        Yii::import("Core.models.SettingParam");
        $model = new SettingParam;
	    $data = $model->find('name = "ADS_IMPORT_ENABLED"');
        if (Yii::app()->request->IsPostRequest) {
            if($_POST['action'] == 'stop') {
                $url = $model->find('name = "ADS_URL_CENTRAL"');
                $key = $model->find('name = "ADS_IMPORT_KEY"');
                if (is_null($url))
                    throw new CHttpException(404,Language::t(Yii::app()->language,'Backend.Ads.Message','Missing parameter Ads Central URL'));
                if (is_null($key))
                    throw new CHttpException(404,Language::t(Yii::app()->language,'Backend.Ads.Message','Missing parameter Ads Import Key'));
                $data->value = 0;
	            $data->save();
	            //$model->updateByPk(212, array('value'=>0));
                $ch = curl_init($url->value.'?r=Core/service/index&SID=Ads.import.stop&key='.$key->value);                                                                                                          
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                                                                              
                curl_exec($ch);
            }
        }
        //$data = $model->findByPk('name = "ADS_IMPORT_ENABLED"');
        if (is_null($data))
            throw new CHttpException(404,Language::t(Yii::app()->language,'Backend.Ads.Message','Missing parameter Ads Import Enabled')); 
        $this->render('get',array('model'=>$data));
    }
    
    public function actionProccessPayment() {
        $action = $this->get('action','');
        if($action == 'return') {
            $tx_token = $this->get('tx','');
            $result = FSM::run('Ads.import.register',array('tx' => $tx_token));
            if (! $result->hasErrors())
                $this->message = Language::t(Yii::app()->language,'Backend.Ads.Message','Payment has been successfully.');
            $this->redirect(array('GetAds'));
        }
    }

    public function filterSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            // description
            'ALLOW_HTML_LINKS',
            // location
            'AREA_LIST',
            // settings
            'PHOTO_MAX_COUNT','CURRENCY','SHOW_VIEW_COUNTER',
            // banned words
            'BANNED_WORDS','MSG_BANNED_CONTENT',
        ));

        $event->params['modules'] = null;

    }
    
    public function actionListingSettings(){
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterListingSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);
        
    }

    public function filterListingSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            // listing
            'MAX_RESULTS','RESULT_SORT','RSS_FEED',
        ));
        
        $event->params['modules'] = null;
    }
    
}
