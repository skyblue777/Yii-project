<?php

class ArticleCommentAPIService extends FServiceBase
{    
    /**
    * Get a ArticleComment model given its ID
    * 
    * @param int id ArticleComment ID
    * @return FServiceModel
    */
    public function get($params){
        $model = ArticleComment::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Article.ArticleComment','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        $model = $this->getModel($params['ArticleComment'],'ArticleComment');
        $this->result->processed('model', $model);
        
        if (! $model->validate()) {
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Article.ArticleComment', 'Submitted data is missing or invalid.'));
            Yii::trace($model->getErrors());
        } elseif ($this->getParam($params, 'validateOnly',0) == TRUE) {
            return $this->result;
        } elseif (! $model->save(false)) {
            // we can save without validation as the model has been validated
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('Article.ArticleComment','Error while saving submitted data into database.'));
        }
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Article.ArticleComment','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = ArticleComment::model()->findByPk($id);
            /**
            * TODO: Check related data if this ArticleComment is deletable
            * This can be done in onBeforeDelete or here or in extensions
            *
            if (Related::model()->count("ArticleCommentId = {$id}") > 0)
                $this->result->fail(ERROR_VIOLATING_BUSINESS_RULES, Yii::t('Article.ArticleComment',"Cannot delete ArticleComment ID={$id} as it has related class data."));
            else
            */
                try {
                    $model->delete();
                } catch (CDbException $ex) {
                    $this->result->fail(ERROR_HANDLING_DB, $ex->getMessage());
                }
        }
        return $this->result;
    }
}