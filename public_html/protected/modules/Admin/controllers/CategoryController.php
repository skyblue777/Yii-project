<?php

class CategoryController extends BackOfficeController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
        $this->actionUpdate();
	}
    public function actiondeleteIcon($id){
        $model = Category::model()->findByAttributes(array("id"=>$id));
        $model->image='';
        if($model->save()){
            echo 'true';
        }else{
            echo 'false';
        }
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
        if (Yii::app()->request->IsPostRequest) {
            if (isset($_POST['uploadNewIcon'])){                
                
                $uploader = CUploadedFile::getInstanceByName('categoryIconUploader');
                if (is_null($uploader)) throw new CHttpException(400,'Upload failed.');  
                $ext=$uploader->getExtensionName();
                //$filename=uniqid(rand(), true).".".$ext;
                $filename=$uploader->name;
                $filePath = 'uploads/category/'.$filename;
                if ($uploader->saveAs($filePath)){
                    $id = $this->get('id', 0);
                    $model = Category::model()->findByAttributes(array("id"=>$id));
                    $model->image=$filename;
                    if($model->save()){
                        //echo 'true';
                    }else{
                        throw new CHttpException(400,'Upload Icon failed.');  
                    }                    
                }
            }elseif( isset($_POST['removeCurrentLogo'])){
                $id = $this->get('id', 0);
                $model = Category::model()->findByAttributes(array("id"=>$id));
                $model->image='';
                if($model->save()){
                    //echo 'true';
                }else{
                    throw new CHttpException(400,'Remove Icon failed.');  
                }                    
            }else{
                //save posted data
                $_POST['validateOnly'] = ($this->post('ajax','') == 'category-form');                
                $result = FSM::run('Core.Category.save', $_POST);
                $model = $result->model;        

                if ($this->post('ajax','') == 'category-form'){
                    echo $result->getActiveErrorMessages($result->model);
                    Yii::app()->end();
                }   
                if (! $result->hasErrors()) {
                    $this->message = Language::t(Yii::app()->language,'Backend.Common.Common','Item has been saved successfully.');
                    $this->redirect(array('update', 'id'=>$model->id));
                }
            }            
        } else {
            // show edit form
            $id = $this->get('id', 0);
            if ($id == 0) {
                $model = new Category();
                $model->warning_page = '';
                $model->show_ad_counter = '';
                $model->price_required = '';
            } else {
                $model = FSM::run('Core.Category.get', array('id' => $id))->model;
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
                $result = FSM::run('Core.Category.delete', array('ids' => $ids));
                if ($result->hasErrors()) {
                    echo $result->getError('ErrorCode');
                } elseif(!Yii::app()->request->isAjaxRequest) {
                    // only redirect user to the admin page if it is not an AJAX request
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
            } else {
                throw new CHttpException(400,Language::t(Yii::app()->language,'Backend.Common.Common','Cannot delete item with the given ID.'));
            }
        } else {
            throw new CHttpException(400,Language::t(Yii::app()->language,'Backend.Common.Common','Invalid request. Please do not repeat this request again.'));
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
        $model=new Category('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Category']))
            $model->attributes=$_GET['Category'];
        
        $criteria=new CDbCriteria;
        $criteria->compare('parent_id', AdsSettings::ADS_ROOT_CATEGORY);
        $criteria->order = 'ordering';
        $models = Category::model()->findAll($criteria);

        Yii::import('Core.utilities.*');
        $this->render('admin',array(
            'models'=>$models,
        ));
	}
    
    protected function renderNestedCategory($viewFile, $models)
    {
        foreach ($models as $index => $model) {
            echo '<li class="sortable" id="items-'.$model->id.'">';
            Yii::app()->controller->renderFile($viewFile, array('class'=>'odd', 'model'=>$model));
            $children = $model->children;
            if (is_array($children) && count($children)) {
                echo '<ul>';
                $this->renderNestedCategory($viewFile, $children);
                echo '</ul>';
            }
            echo '</li>';
        }
    }
    public function actionUploadIcon()
    {

    }
}
