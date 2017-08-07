<?php

class ButtonsSettingsController extends BackOfficeController
{
    public function actionEdit() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterButtonColorSettingsParams'));
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterButtonColorSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'BUTTON_COLOR','BACKGROUND_COLOR'
        ));

        $event->params['modules'] = null;
    }    
}