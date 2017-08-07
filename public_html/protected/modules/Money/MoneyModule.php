<?php

class MoneyModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Money.models.*',
			'Money.components.*',
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
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Top Ads'),'url'=>array('/Money/Money/topAdsSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Homepage Gallery'),'url'=>array('/Money/Money/homePageGallerySettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Paid Ads'),'url'=>array('/Money/Money/paidAdsSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Banners'),'url'=>array('/Money/Money/bannersSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Adsense'),'url'=>array('/Money/Money/adsenseSettings')),
                )
            ),
        );
    }
}
