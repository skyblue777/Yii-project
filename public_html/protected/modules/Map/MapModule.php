<?php

class MapModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Map.models.*',
			'Map.components.*',
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
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','General'),'url'=>array('/Map/Map/generalSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Homepage'),'url'=>array('/Map/Map/homePageSettings')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Ads'),'url'=>array('/Map/Map/adsSettings')),
                )
            ),
        );
    }
}
