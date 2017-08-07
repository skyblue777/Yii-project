<?php

class ArticleModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Article.models.*',
			'Article.components.*',
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
            array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Manage article|View and search articles'),'url'=>array('/Article/admin/article/admin')),
            array('label'=>Language::t(Yii::app()->language,'Backend.Common.Menu','Write a new article'),'url'=>array('/Article/admin/article/create')),
        );
    }
}
