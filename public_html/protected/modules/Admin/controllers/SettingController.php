<?php

class SettingController extends BackOfficeController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>FSM::run('Admin.SettingParam.get', array('id'=>$id))->model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
        $this->actionUpdate();
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
        if (Yii::app()->request->IsPostRequest) {
            // save posted data
            $_POST['validateOnly'] = ($this->post('ajax','') == 'setting-param-form');
            $result = FSM::run('Admin.SettingParam.save', $_POST);
            $model = $result->model; 

            if ($this->post('ajax','') == 'setting-param-form'){
                echo $result->getActiveErrorMessages($result->model);
                Yii::app()->end();
            }   
            if (! $result->hasErrors())
                $this->message = Yii::t('Core','Item has been saved successfully.');
        } else {
            // show edit form
            $id = $this->get('id', 0);
            if ($id == 0) {
                $model = new SettingParam();
            } else {
                $model = FSM::run('Admin.SettingParam.get', array('id' => $id))->model;
            }
        }
        
        $modules = $model->getModules();
        //remove sytem ~ '' empty module
        if (count($modules))
            array_pop($modules);
            
        $this->render('update', array('model' => $model, 'modules'=>$modules));
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
                $result = FSM::run('Admin.SettingParam.delete', array('ids' => $ids));
                if ($result->hasErrors()) {
                    echo $result->getError('ErrorCode');
                } elseif(!Yii::app()->request->isAjaxRequest) {
                    // only redirect user to the admin page if it is not an AJAX request
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
            } else {
                throw new CHttpException(400,Yii::t('Core','Cannot delete item with the given ID.'));
            }
        } else {
            throw new CHttpException(400,Yii::t('Core','Invalid request. Please do not repeat this request again.'));
        }
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $model=new SettingParam('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_POST['SettingParam']))
            // we don't need to explicit saving the param here. The widget used by the view will do that
            // kind of weird implement !
            $model->attributes=$_POST['SettingParam'];
        else
            $model->module = 'system_module';
            
        $model->visible = 1;
        $dataProvider=$model->search();
        $dataProvider->pagination = false;
        $criteria = $dataProvider->getCriteria();
        
        $modules = $model->getModules();

        $event = new CEvent($this, array(
            'criteria' => $criteria,
            'modules' => $modules,
        ));
        $this->OnBeforeFindSettingParameters($event);
        $modules = $event->params['modules'];      
        $dataProvider->criteria = $event->params['criteria'];
        
        $params = $dataProvider->getData();

        //TODO: load module param definitions into $config
        
        $config = include(Yii::getPathOfAlias('application.config').'/params.php');
        $this->render('index',array(
            'modules'=>$modules,
            'module'=>$model->module,
            'params'=>$params,
            'config'=>$config,
        ));
    }
    
    public function OnBeforeFindSettingParameters($event) {
        $this->raiseEvent('OnBeforeFindSettingParameters', $event);
    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $model=new SettingParam('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['SettingParam']))
            $model->attributes=$_GET['SettingParam'];

        $this->render('admin',array(
            'model'=>$model,
        ));
	}
    
    public function actionOrder()
    {
        $result = FSM::run('Admin.SettingParam.saveOrder', $_GET);
        echo $result->toJson();
    }
    
    public function actionRebuildCache()
    {
        FSM::run('Core.Settings.rebuildCache');
        $this->message = Yii::t('Core','Rebuild cache successfully.');
        $this->redirect(array('/Admin'));
    }
}
