<?php

class SettingParamService extends FServiceBase
{    
    /**
    * Get a SettingParam model given its ID
    * 
    * @param int id SettingParam ID
    * @return FServiceModel
    */
    public function get($params){
        $model = SettingParam::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.SettingParam','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        /**
        * @var CModel
        */
        $model = $this->getModel($params['SettingParam'],'SettingParam');
        $this->result->processed('model', $model);
        
        $isNew = ! $model->id;
        
        if (! $model->validate())
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.SettingParam', 'Submitted data is missing or invalid.'));
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        elseif (! $model->save())
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('Admin.SettingParam','Error while saving submitted data into database.'));
        else {
            //rebuild cache Constant Settings class
            FSM::run('Core.Settings.db2php', array('module'=>$model->module));
            
            if ($isNew == false) {
                //if change module, rebuild source module
                $oldModule = $this->getParam($params, 'oldModule', '');
                Yii::trace('$oldModule '.$oldModule.' module '.$model->module);
                if ($oldModule !== $model->module)
                    FSM::run('Core.Settings.db2php', array('module'=>$oldModule));
            }
        }
        
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Admin.SettingParam','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = SettingParam::model()->findByPk($id);
            try {
                $module = $model->module;
                if ($model->delete()) {
                    //rebuild cache Constant Settings class
                    FSM::run('Core.Settings.db2php', array('module'=>$module));
                }
            } catch (CDbException $ex) {
                $this->result->fail(ERROR_HANDLING_DB, $ex->getMessage());
            }
        }
        return $this->result;
    }
    
    public function saveOrder($params) {
        $ids = $this->getParam($params, 'ids', array());
        if (count($ids)) {
            foreach ($ids as $index => $id) {
                SettingParam::model()->updateByPk($id, array('ordering'=>$index+1));
            }
        }
        
        return $this->result;
    }
}