<?php

class MapController extends BackOfficeController
{
    public function actionGeneralSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterGeneralSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterGeneralSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'GAPI','LATITUDE','LONGITUDE'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionHomePageSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterHomePageSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterHomePageSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'DISPLAY_MAP_HOMEPAGE','MAP_TYPE','MAP_ZOOM','MAP_MARKER','ZOOM'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionAdsSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterAdsSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterAdsSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'DISPLAY_MAP_ADS','MAP_TYPE_ADS','MAP_ZOOM_ADS','ZOOM_ADS'
        ));

        $event->params['modules'] = null;
    }
}
