<?php

class AdsModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Ads.models.*',
			'Ads.components.*',
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
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','All'),'url'=>array('/Ads/Ads/list','Annonce_sort'=>'create_time.desc')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Settings'),'url'=>array('/Ads/Ads/settings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Listing settings'),'url'=>array('/Ads/Ads/listingSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Get Ads'),'url'=>array('/Ads/Ads/getAds')),
                )
            ),
            array(
                'title' => 'Categories',
                'items' => array(
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Manage'),'url'=>array('/Admin/category/admin')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Add'),'url'=>array('/Admin/category/create')),
                    array('label'=>'','url'=>array('/Admin/category/update')),
                )
            ),
        );
    }
}
