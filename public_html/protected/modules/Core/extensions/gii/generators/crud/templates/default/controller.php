<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
$module = $this->Module;
$moduleId = $module instanceof CModule ? $module->Id : 'application';
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass."\n"; ?>
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
			'model'=>FSM::run('<?php echo $moduleId; ?>.<?php echo $this->modelClass; ?>API.get', array('id'=>$id))->model,
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
            $_POST['validateOnly'] = ($this->post('ajax','') == '<?php echo $this->class2id($this->modelClass); ?>-form');
            $result = FSM::run('<?php echo $moduleId; ?>.<?php echo $this->modelClass; ?>API.save', $_POST);
            $model = $result->model; 

            if ($this->post('ajax','') == '<?php echo $this->class2id($this->modelClass); ?>-form'){
                echo $result->getActiveErrorMessages($result->model);
                Yii::app()->end();
            }   
            if (! $result->hasErrors()) {
                $this->message = Yii::t('Core','Item has been saved successfully.');
                $this->redirect(array('update', 'id'=>$model->id));
            }
        } else {
            // show edit form
            $id = $this->get('id', 0);
            if ($id == 0) {
                $model = new <?php echo $this->modelClass; ?>();
            } else {
                $model = FSM::run('<?php echo $moduleId; ?>.<?php echo $this->modelClass; ?>API.get', array('id' => $id))->model;
            }
        }
            
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
                $result = FSM::run('<?php echo $moduleId; ?>.<?php echo $this->modelClass; ?>API.delete', array('ids' => $ids));
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
        $model=new <?php echo $this->modelClass; ?>('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['<?php echo $this->modelClass; ?>']))
            $model->attributes=$_GET['<?php echo $this->modelClass; ?>'];

        $this->render('admin',array(
            'model'=>$model,
        ));
	}
}
