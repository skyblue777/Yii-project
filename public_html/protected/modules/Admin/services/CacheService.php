<?php

class CacheService extends FServiceBase
{    
    /**
    * Get a Cache model given its ID
    * 
    * @param int id Cache ID
    * @return FServiceModel
    */
    public function get($params){
        $model = Cache::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        /**
        * @var CModel
        */
        $model = $this->getModel($params['Cache'],'Cache');
        $this->result->processed('model', $model);
        
        if (! $model->validate())
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache', 'Submitted data is missing or invalid.'));
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        elseif (! $model->save())
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('Admin.Cache','Error while saving submitted data into database.'));
        
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = Cache::model()->findByPk($id);
            /**
            * TODO: Check related data if this Cache is deletable
            * This can be done in onBeforeDelete or here or in extensions
            *
            if (Related::model()->count("CacheId = {$id}") > 0)
                $this->result->fail(ERROR_VIOLATING_BUSINESS_RULES, Yii::t('Admin.Cache',"Cannot delete Cache ID={$id} as it has related class data."));
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
    
    public function status($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $value = $this->getParam($params, 'value', null);
        if (!$id || !isset($value))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Ddata is missing or invalid.'));
        
        Cache::model()->updateByPk($id, array('expired'=>$value));
        
        return $this->result;
    }
    
    public function create($params)
    {
        $name = $this->getParam($params, 'name', '');
        $description = $this->getParam($params, 'description', '');
        if (empty($name))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Data is missing or invalid.'));
        if (!Cache::model()->countByAttributes(array('name'=>$name))) {
            $model = new Cache;
            $model->name = $name;
            $model->description = $description;
            $model->expired = date('Y-m-d H:i:s');
            if (!$model->save()) {
                $this->result->fail(ERROR_HANDLING_DB, Yii::t('Admin.Cache','Error while saving submitted data into database.'));
            }
        }
        return $this->result;
    }
    
    public function getByName($params){
        $model = Cache::model()->findByAttributes(array('name'=>$this->getParam($params, 'name', '')));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Invalid name.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function getCacheDependency($params)
    {
        $name = $this->getParam($params, 'name', '');
        if (empty($name))
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Data is missing or invalid.'));
        
        $this->create($params);
        
        $sql = "SELECT expired FROM cache WHERE name='".$name."'";
        $dependency = new CDbCacheDependency($sql);
        $this->result->processed('dependency', $dependency);
        return $this->result;
    }
    
    public function getExpire($params)
    {
        $result = $this->getByName($params);
        $duration = -1;
        if (!$result->hasErrors()) {
            $duration = $result->model->duration;
        }
        //use global expire when duration = -1
        if ($duration < 0) {
            if (defined('Settings::CACHE_EXPIRE') && Settings::CACHE_EXPIRE >= 0)
                $duration = Settings::CACHE_EXPIRE;
            else
                $duration = 0;
        }
        $this->result->processed('duration', $duration);
        return $this->result;
    }
    
    public function rebuildCacheByName($params)
    {
        $name = $this->getParam($params, 'name', '');
        if (empty($name))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Cache','Data is missing or invalid.'));
        
        Cache::model()->updateAll(array(
            'expired'=>date('Y-m-d H:i:s')), 
            'name=:name', 
            array(
                ':name'=>$name
            )
        );
        return $this->result;
    }
}