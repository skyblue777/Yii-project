<?php

class SettingsController extends BackOfficeController
{
    public function actionGeneral() {
        // remove logo or upload logo
        if (Yii::app()->request->isPostRequest)
        {
            // remove logo
            if (isset($_POST['removeCurrentLogo']))
            {
                $logoParam = SettingParam::model()->find('name=:name',array(':name'=>'SITE_LOGO'));
                if (is_null($logoParam))
                    throw new CHttpException(400,'Parameter Logo is not found');
                $currentLogo = $logoParam->value;
                $logoParam->value = 'none';
                $logoParam->update(array('value'));
                // remove file
                $logoPath = 'uploads/'.$currentLogo;
                if (file_exists($logoPath)) unlink($logoPath);
                
                FSM::run('Core.Settings.db2php', array('module'=>''));
                
                $this->redirect(array('/Admin/settings/general'));        
            }
            // upload logo
            if (isset($_POST['uploadNewLogo']))
            {
                $logoParam = SettingParam::model()->find('name=:name',array(':name'=>'SITE_LOGO'));
                if (is_null($logoParam))
                    throw new CHttpException(400,'Parameter Logo is not found');
                
                $uploader = CUploadedFile::getInstanceByName('siteLogoUploader');
                if (is_null($uploader)) throw new CHttpException(400,'Upload failed.'); 
                $filename = 'logo_'.$uploader->name;
                $filePath = 'uploads/'.$filename;
                if ($uploader->saveAs($filePath))
                {
                    // remove current logo file
                    if ($logoParam->value!='' && $logoParam->value!='none')
                    {
                        $currentLogoPath = 'uploads/'.$logoParam->value;
                        if (file_exists($currentLogoPath)) unlink($currentLogoPath);   
                    }
                    // update new logo file
                    $logoParam->value = $filename;
                    $logoParam->update(array('value'));
                    FSM::run('Core.Settings.db2php', array('module'=>''));
                    $this->redirect(array('/Admin/settings/general'));    
                }
                else
                    throw new CHttpException(400,'Logo is not uploaded successfully');       
            }    
        }
        
        list($controller,$actionId) = Yii::app()->createController('/Admin/Setting/index');
        $controller->attachEventHandler('OnBeforeFindSettingParameters', array($this,'filterGeneralSettingsParams'));
        
        $controller->init();
        $controller->run($actionId);
        
        if (Yii::app()->request->isPostRequest && !isset($_POST['removeCurrentLogo']) && !isset($_POST['uploadNewLogo']))
        {
            FSM::run('Core.Settings.db2php', array('module'=>''));        
        }   
    }
    
    public function filterGeneralSettingsParams($event){
        /**
        * @var CDbCriteria
        */
        $criteria = &$event->params['criteria'];
        $criteria->condition = '';
        $criteria->order = 'ordering';
        $criteria->addInCondition('name',array(
            'SITE_LOGO','SITE_NAME','DEFAULT_META_DESCRIPTION','SITE_CONTACT','EXPIRATION','SITE_ACCESS','GOOGLE_CODE'
        ));

        $event->params['modules'] = null;
    }
}
