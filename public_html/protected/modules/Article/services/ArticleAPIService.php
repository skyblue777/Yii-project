<?php

class ArticleAPIService extends FServiceBase
{    
    /**
    * Get a Article model given its ID
    * 
    * @param int id Article ID
    * @return FServiceModel
    */
    public function get($params){
        $model = Article::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Article.Article','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        $model = $this->getModel($params['Article'],'Article', array(
            'leading_text' => 'notag',
            'content' => 'notag'
        ));
        $this->result->processed('model', $model);
        
        if (! $model->validate()) {
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
            Yii::trace($model->getErrors());
        } elseif ($this->getParam($params, 'validateOnly',0) == TRUE) {
            return $this->result;
        } elseif (! $model->save(false)) {
            // we can save without validation as the model has been validated
            $this->result->fail(ERROR_HANDLING_DB, Language::t(Yii::app()->language,'Frontend.Ads.Message','Error while saving submitted data into database.'));
        }
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = Article::model()->findByPk($id);
            /**
            * TODO: Check related data if this Article is deletable
            * This can be done in onBeforeDelete or here or in extensions
            *
            if (Related::model()->count("ArticleId = {$id}") > 0)
                $this->result->fail(ERROR_VIOLATING_BUSINESS_RULES, Yii::t('Article.Article',"Cannot delete Article ID={$id} as it has related class data."));
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
    
    /**
    * List all tags in article sphere that contain the search term
    * 
    * @param string $term the search term
    */
    public function searchTags($params = array()) {
        $term = $this->getParam($params, 'term', '');
        $tag = new ArticleTag('search');
        $tag->name = $term;
        
        $this->result->processed('matches', $tag->search()->Data);
        return $this->result;
    }
    
    
    public function setStatus($params = array()) {
        $status = $this->getParam($params, 'status', '');
        $id = $this->getParam($params, 'id', 0);
        
        if ($status != 'active' && $status != 'inactive')
            return $this->result->fail(0, 'Invalid status.');
        
        $ret = Article::model()->updateByPk($id, array('status' => ($status == 'active' ? 1 : 2)));
        $this->result->processed('success', $ret);
    }
}
