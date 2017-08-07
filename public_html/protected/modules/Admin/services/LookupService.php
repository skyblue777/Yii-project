<?php

class LookupService extends FServiceBase
{    
    /**
    * Get a Lookup model given its ID
    * 
    * @param int id Lookup ID
    * @return FServiceModel
    */
    public function get($params){
        $model = Lookup::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Lookup','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        /**
        * @var CModel
        */
        $model = $this->getModel($params['Lookup'],'Lookup');
        $this->result->processed('model', $model);
        
        if (! $model->validate())
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Lookup', 'Submitted data is missing or invalid.'));
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        elseif (! $model->save())
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('Admin.Lookup','Error while saving submitted data into database.'));
        
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.Lookup','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = Lookup::model()->findByPk($id);
            /**
            * TODO: Check related data if this Lookup is deletable
            * This can be done in onBeforeDelete or here or in extensions
            *
            if (Related::model()->count("LookupId = {$id}") > 0)
                $this->result->fail(ERROR_VIOLATING_BUSINESS_RULES, Yii::t('Admin.Lookup',"Cannot delete Lookup ID={$id} as it has related class data."));
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