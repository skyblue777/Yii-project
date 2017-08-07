<?php
Yii::import('Article.models.Article');
class PostController extends FrontController
{
    public function getGuestAllowedActions() {
        return array('index','view');    
    }
    
    public function actionIndex() {
        $model=new Article('search');
        $model->unsetAttributes();  // clear any default values
        
        $criteria = new CDbCriteria();
        $criteria->compare('t.status', Article::STATUS_ACTIVE);
        $criteria->with = array('category','author');
        $criteria->order = 't.update_time DESC';
        
        $dataSource = new CActiveDataProvider('Article', array(
            'criteria' => $criteria,
        ));
        
        $this->render('list',array(
            'model'=>$model,
            'dataSource'=>$dataSource,
        ));        
    }
    
    public function actionView() {
        $result = FSM::run('Article.ArticleAPI.get', $_GET);
        if ($result->hasErrors()) {
            
        } else {
            $this->render('view', array(
                'model' => $result->model,
            ));
        }
    }
}
