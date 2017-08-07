<?php

class MessagingModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Messaging.models.*',
			'Messaging.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
    
    public function getMenus(){
        return array(
            array(
                'items'=>array(
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Activation'),'url'=>array('/Messaging/messagingSettings/editEmailTemplate','template'=>'activation_email')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Expiration'),'url'=>array('/Messaging/messagingSettings/editEmailTemplate','template'=>'expriation_email')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Registration'),'url'=>array('/Messaging/messagingSettings/editEmailTemplate','template'=>'registration_email')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Reply to ad'),'url'=>array('/Messaging/messagingSettings/editEmailTemplate','template'=>'reply_to_ad')),
                    //array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Email to friend'),'url'=>array('/Messaging/messagingSettings/editEmailTemplate','template'=>'email_ad_to_friend')),
                )
            ),
        );
    }
}
