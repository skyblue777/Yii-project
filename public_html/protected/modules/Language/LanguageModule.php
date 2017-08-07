<?php

class LanguageModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Language.models.*',
			'Language.components.*',
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
            	'title'=>'Language',
                'items'=>array(
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','General'),'url'=>array('/Language/language/general')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Translate'), 'url'=>array('/Language/language/translate')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Add/Delete'), 'url'=>array('/Language/language/manager')),
                )
            ),
            array(
                'title' => 'Settings',
                'items' => array(
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','General'),'url'=>array('/Admin/settings/general')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Static Pages'),'url'=>array('/Article/admin/article')),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Edit account'),'url'=>array('/User/account/update','id'=>Yii::app()->user->id)),
                    array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Manage administrators'),'url'=>array('/User/account/adminList')),
                )
            ),
        );
    }
}

