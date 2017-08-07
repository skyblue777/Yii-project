<?php

class ArticleController extends BackOfficeController
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
			'model'=>FSM::run('Article.ArticleAPI.get', array('id'=>$id))->model,
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
            $_POST['validateOnly'] = ($this->post('ajax','') == 'article-form');
            $result = FSM::run('Article.ArticleAPI.save', $_POST);
            $model = $result->model; 

            if ($this->post('ajax','') == 'article-form'){
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
                $model = new Article();
            } else {
                $model = FSM::run('Article.ArticleAPI.get', array('id' => $id))->model;
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
                $result = FSM::run('Article.ArticleAPI.delete', array('ids' => $ids));
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
        $model=new Article('search');
        $model->unsetAttributes();  // clear any default values
        $model->lang=Yii::app()->language;   
        if(isset($_GET['Article']))
            $model->attributes=$_GET['Article'];
        $model->author_name = $this->get('Article[author_name]','');
            
        $dataSource = $model->search();
        // modify search criteria to show category name
        $criteria = $dataSource->getCriteria();
        $criteria->with = array('category','author');
        $criteria->compare('author.username',$model->author_name, true);
        
        $sort = new CSort('Article');
        $sort->attributes = array(
            '*',
            'author_name' => array(
                'asc' => 'author.username asc',
                'desc' => 'author.username desc'
            )
        );
        
        $dataSource->sort = $sort;
        
        $this->render('admin',array(
            'model'=>$model,
            'dataSource'=>$dataSource,
        ));
	}
}
