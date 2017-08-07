<?php

/**
 * This is the model class for table "setting".
 */
Yii::import('Core.models.base.SettingParamBase');
class SettingParam extends SettingParamBase
{
    
    public $rules = array();
    
    public function rules(){
        return $this->rules;
    }
    
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'label' => 'Label',
            'value' => Language::t(Yii::app()->language,'Backend.Admin.Setting',$this->label),
            'description' => 'Description',
            'setting_group' => 'Setting Group',
            'ordering' => 'Ordering',
            'visible' => 'Visible',
            'module' => 'Module',
        );
    }
        
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;
        
        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('label',$this->label,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('description',$this->description,true);
        if ($this->setting_group == 'ungroup')
            $criteria->addCondition("setting_group IS NULL OR setting_group = ''");
        else
            $criteria->compare('setting_group',$this->setting_group,true);
        if ($this->module == 'system_module')
            $criteria->addCondition("module IS NULL OR module = ''");
        else
            $criteria->compare('module',$this->module,true);
            
        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'module, setting_group, ordering, name'
            )
        );
        
//        if (empty($this->setting_group) === false && empty($this->module) === false) {
            $config['pagination'] = array('pageSize'=>1000);
//        }
        
        return new CActiveDataProvider(get_class($this), $config);
    }
    
    public function getModules()
    {
        $modules = $this->getDbConnection()->createCommand('SELECT DISTINCT module FROM setting')->queryColumn();
        if (count($modules)){
            $modules = array_filter($modules, 'strlen');
            $modules = array_combine($modules, $modules);
            $modules['system_module'] = 'System';
        }
        return $modules;
    }
    
    public function getGroups()
    {
        $modules = $this->getDbConnection()->createCommand('SELECT DISTINCT setting_group FROM setting')->queryColumn();
        if (count($modules)){
            $modules = array_filter($modules, 'strlen');
            $modules = array_combine($modules, $modules);
            $modules['ungroup'] = 'Ungroup';
        }
        return $modules;
    }
}