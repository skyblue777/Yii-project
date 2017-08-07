<?php

class AccountController extends BackOfficeController
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
			'model'=>FSM::run('User.User.get', array('id'=>$id))->model,
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
     * Creates a new admin.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreateAdmin()
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
            $_POST['validateOnly'] = ($this->post('ajax','') == 'user-form');
            $result = FSM::run('User.User.save', $_POST);
            $model = $result->model; 

            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($result->model);
                Yii::app()->end();
            }   
            if (! $result->hasErrors())
                $this->message = Yii::t('Core','User has been saved successfully.');
        } else {
            // show edit form
            $id = $this->get('id', 0);
            if ($id == 0) {
                $model = new User('register');
            } else {
                $model = User::model()->findByPk($id);
                if (is_null($model))
                    throw new CHttpException(400,'Sorry! Your account does not exist');
            }
        }
        if (!$model->isNewRecord)
            $model->setScenario('edit');
        $model->password = '';
        $model->confirmPassword = '';
            
        $this->render('update', array('model' => $model));
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
                $result = FSM::run('User.User.delete', array('ids' => $ids));
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
        $this->actionAdmin();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $model=new User('search');
        $model->unsetAttributes();
        $model->status = '';
        
        if (Yii::app()->request->isAjaxRequest)
        {
            if(isset($_GET['User']))
                $model->attributes=$_GET['User'];
        }

        $this->render('admin',array(
            'model'=>$model,
        ));
	}
    
    /**
     * Manages all administrators.
     */
    public function actionAdminList()
    {
        $model=new User('search');
        $model->unsetAttributes();
        $model->status = '';
        
        if (Yii::app()->request->isAjaxRequest)
        {
            if(isset($_GET['User']))
                $model->attributes=$_GET['User'];
        }

        $this->render('admin_list',array(
            'model'=>$model,
        ));
    }
    
    public function actionStatus()
    {
        $result = FSM::run('User.User.changeStatus', $_GET);
        echo $result->toJson();
    }
    
    public function actionExportUserList()
    {        
        $status = '';
        if (!isset($_GET['status']) || ($_GET['status'] != '' && !in_array($_GET['status'],array('0','1'))))
            throw new CHttpException(400, Yii::t('Core', 'Sorry! Export has error!'));
        $status = trim($_GET['status']);
        $role = $this->get('role','user');
        if ($role!='admin' && $role!='user')
            throw new CHttpException(400, 'Invalid data');    
        
        $con = Yii::app()->db;
        
        $sql = "SELECT email, first_name, last_name, created_date FROM user";
        if ($role=='admin')
            $sql .= " INNER JOIN AuthAssignment ON AuthAssignment.userid = user.id WHERE AuthAssignment.itemname = 'administrators'";
        else
            $sql .= " WHERE (SELECT COUNT(*) FROM AuthAssignment WHERE AuthAssignment.userid = user.id AND AuthAssignment.itemname = 'administrators') <= 0";
        
        if ($status != '')
            $sql .= " AND user.status = {$status}";
        
        $com = $con->createCommand($sql);
        $users = $com->queryAll(TRUE);
        
        if (is_array($users) && count($users) > 0)
        {
            $csvContent = "E-mail, First Name, Last Name, Registration date \n";
            foreach($users as $user)
            {
                $data = array($user['email'],$user['first_name'],$user['last_name'],date('m/d/Y',strtotime($user['created_date'])));
                $csvContent .= implode(',',$data).", \n";
            }
            
            if ($status == '')
                $filename = 'user_list.csv';
            elseif ($status == 1)
                $filename = 'registered_user_list.csv';
            else
                $filename = 'non_registered_user_list.csv';
                
            $outputFile = Yii::getPathOfAlias('application')."/runtime/exported/".$filename;
            file_put_contents($outputFile, $csvContent);

            header("Content-type: text/html; charset=UTF-8");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $this->downloadFile($outputFile);   
        }    
    }
    
    protected function downloadFile($fullpath)
    {
        if (!file_exists($fullpath))
            throw new Exception('File not found.');
            
        $this->downloadContent($fullpath, file_get_contents($fullpath));
    }
      
    protected function downloadContent($filename, $content)
    {
        $path_parts = pathinfo($filename);
        $ext = strtolower($path_parts["extension"]);
       
        // Determine Content Type
        switch ($ext) {
          case "pdf": $ctype="application/pdf"; break;
          case "exe": $ctype="application/octet-stream"; break;
          case "zip": $ctype="application/zip"; break;
          case "doc": $ctype="application/msword"; break;
          case "xls": $ctype="application/vnd.ms-excel"; break;
          case "csv": $ctype="application/vnd.ms-excel"; break;
          case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
          case "gif": $ctype="image/gif"; break;
          case "png": $ctype="image/png"; break;
          case "jpeg":
          case "jpg": $ctype="image/jpg"; break;
          default: $ctype="application/force-download";
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
        header("Content-Transfer-Encoding: binary");

        while (@ob_end_clean());

        echo $content;
    }
    
    public function actionConfigBan()
    {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterBanParams'));
        
        $controller->init();
        $controller->run($actionId);
    }
    
    public function filterBanParams($event){
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'BANNED_EMAIL','BANNED_IP','MSG_BANNED_USER',
        ));
        
        $event->params['modules'] = null;
    }
}
