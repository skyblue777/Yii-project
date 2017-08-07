<?php

class MoneyController extends BackOfficeController
{
    public function actionTopAdsSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterTopAdsSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterTopAdsSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'TOP_TIME1','TOP_TIME2','TOP_PRICE1','TOP_PRICE2','PAYPAL_EMAIL_TOP','PAYPAL_PDT_TOP','PAYPAL_CURRENCY_TOP'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionHomePageGallerySettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterHomePageGallerySettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterHomePageGallerySettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'HG_TIME1','HG_TIME2','HG_PRICE1','HG_PRICE2','PAYPAL_EMAIL_HG','PAYPAL_PDT_HG','PAYPAL_CURRENCY_HG'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionPaidAdsSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterPaidAdsSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterPaidAdsSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'PAID_ADS_PRICE','PAYPAL_EMAIL_PAID','PAYPAL_PDT_PAID','PAYPAL_CURRENCY_PAID'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionBannersSettings() {
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterBannersSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterBannersSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'BANNER_HOMEPAGE_CODE','BANNER_HOMEPAGE_PLACEMENT','BANNER_LISTINGPAGES_CODE','BANNER_LISTINGPAGES_PLACEMENT','BANNER_ADPAGE_CODE','BANNER_ADPAGE_PLACEMENT'
        ));

        $event->params['modules'] = null;
    }
    
    public function actionAdsenseSettings() {
        if (Yii::app()->request->isPostRequest)
        {
            if (isset($_POST['ADSENSE_CODE'])) SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_CODE']),'name=:name',array(':name'=>'ADSENSE_CODE'));
            
            if (isset($_POST['ADSENSE_HOMEPAGE_TOP_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_HOMEPAGE_TOP_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_HOMEPAGE_TOP_PLACEMENT'));
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_HOMEPAGE_TOP_PLACEMENT'));
                       
            if (isset($_POST['ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT'));
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT'));    
            
            if (isset($_POST['ADSENSE_LISTINGPAGES_TOP_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_LISTINGPAGES_TOP_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_LISTINGPAGES_TOP_PLACEMENT'));
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_LISTINGPAGES_TOP_PLACEMENT'));    
                
            if (isset($_POST['ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT'));
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT'));
                    
            if (isset($_POST['ADSENSE_ADPAGE_TOP_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_ADPAGE_TOP_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_ADPAGE_TOP_PLACEMENT'));
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_ADPAGE_TOP_PLACEMENT'));
                
            if (isset($_POST['ADSENSE_ADPAGE_BOTTOM_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_ADPAGE_BOTTOM_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_ADPAGE_BOTTOM_PLACEMENT')); 
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_ADPAGE_BOTTOM_PLACEMENT'));    
                
            if (isset($_POST['ADSENSE_ADPAGE_MIDDLE_PLACEMENT']))
                SettingParam::model()->updateAll(array('value'=>$_POST['ADSENSE_ADPAGE_MIDDLE_PLACEMENT']),'name=:name',array(':name'=>'ADSENSE_ADPAGE_MIDDLE_PLACEMENT')); 
            else
                SettingParam::model()->updateAll(array('value'=>0),'name=:name',array(':name'=>'ADSENSE_ADPAGE_MIDDLE_PLACEMENT'));    
            
            FSM::run('Core.Settings.db2php', array('module'=>'Money'));
            $this->redirect(array('/Money/Money/adsenseSettings'));
        }
        
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterAdsenseSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);   
    }
    
    public function filterAdsenseSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'ADSENSE_CODE'
        ));

        $event->params['modules'] = null;
    }
}
