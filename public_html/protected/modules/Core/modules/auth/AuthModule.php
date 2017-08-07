<?php
/**
* Rights module class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @version 0.9.9
*/
class AuthModule extends FWebModule
{
	/**
	* @var string the name of the role with superuser priviledges.
	*/
	public $superuserName = 'Admin';
	/**
	* @var string the name of the guest role.
	*/
	public $authenticatedName = 'Authenticated';
	/**
	* @var string the name of the guest role.
	*/
	public $guestName = 'Guest';
	/**
	* @var array list of default roles.
	*/
	public $defaultRoles = null;
	/**
	* @var string the name of the user model class.
	*/
	public $userClass = 'FUser';
	/**
	* @var string the name of the id column in the user table.
	*/
	public $userIdColumn = 'Id';
	/**
	* @var string the name of the username column in the user table.
	*/
	public $userNameColumn = 'Username';
	/**
	* @var boolean whether to enable business rules.
	*/
	public $enableBizRule = true;
	/**
	* @var boolean whether to enable data for business rules.
	*/
	public $enableBizRuleData = false;
	/**
	* @var string the flash message key to use for success messages.
	*/
	public $flashSuccessKey = 'RightsSuccess';
	/**
	* @var string the flash message key to use for error messages.
	*/
	public $flashErrorKey = 'RightsError';
	/**
	* @var boolean whether to install rights when accessed.
	*/
	public $install = false;
	/**
	* @var string the base url to Core.auth. Override when module is nested.
	*/
	public $baseUrl = '/rights';
	/**
	* @var string that path to the layout file to use for displaying Core.auth.
	*/
	public $layout;
	/**
	* @var string the style sheet file to use for Core.auth.
	*/
	public $cssFile;

	private $_assetsUrl;

	/**
	* Initializes the "rights" module.
	*/
	public function init()
	{
		// Set required classes for import
		$this->setImport(array(
			'auth.models.*',
			'auth.components.*',
			'auth.components.dataproviders.*',
			'auth.controllers.*',
		));

		// Set the user identity guest name
		Yii::app()->getUser()->guestName = $this->guestName;

		// Set guest role as the default
		// if the default roles are not set
		if( $this->defaultRoles===null )
			$this->defaultRoles = Yii::app()->authManager->defaultRoles;

		// Set the components component
		$this->setComponents(array(
			'authorizer'=>array(
				'class'=>'AuthAuthorizer',
				'superuserName'=>$this->superuserName,
				'defaultRoles'=>$this->defaultRoles,
			),
			'generator'=>array(
				'class'=>'RightsGenerator',
			),
		));

//		// Set the installer if necessary
//		if( $this->install===true )
//		{
//			$this->setComponents(array(
//				'installer'=>array(
//					'class'=>'RightsInstaller',
//					'superuserName'=>$this->superuserName,
//					'authenticatedName'=>$this->authenticatedName,
//					'guestName'=>$this->guestName,
//					'defaultRoles'=>$this->defaultRoles,
//				),
//			));

//			$this->defaultController = 'install';
//		}

		// Default layout is used unless one is provided
//		if( $this->layout===null )
//			$this->layout = 'Core.auth.views.layouts.rights';
	}

	/**
	* Registers the necessary scripts.
	*/
	public function registerScripts()
	{
		// Publish the necessary paths
		$app = Yii::app();
		$assetsUrl = $this->getAssetsUrl();
		$juiUrl = $app->getAssetManager()->publish(Yii::getPathOfAlias('system.vendors.jqueryui'));

		// Register the necessary scripts
		$cs = $app->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($juiUrl.'/js/jquery-ui.min.js');
		$cs->registerScriptFile($assetsUrl.'/js/rights.js');

		// Make sure we want to register a style sheet
		if( $this->cssFile!==false )
		{
			// Default style sheet is used unless one is provided
			if( $this->cssFile===null )
				$this->cssFile = $assetsUrl.'/css/rights.css';
			else
				$this->cssFile = Yii::app()->request->baseUrl.$this->cssFile;

			// Register the style sheet
			$cs->registerCssFile($this->cssFile);
		}
	}

	/**
	* @return string the base URL that contains all published asset files of Core.auth.
	*/
	public function getAssetsUrl()
	{
		if( $this->_assetsUrl===null )
		{
			//$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('Core.auth.assets'), false, -1, true);
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('auth.assets')); // For release
		}

		return $this->_assetsUrl;
	}

	/**
	* @return RightsAuthorizer the authorizer component.
	*/
	public function getAuthorizer()
	{
		return $this->getComponent('authorizer');
	}

	/**
	* @return RightsInstaller the installer component.
	*/
	public function getInstaller()
	{
		return $this->getComponent('installer');
	}

	/**
	* @return RightsGenerator the generator component.
	*/
	public function getGenerator()
	{
		return $this->getComponent('generator');
	}

	/**
	* @return the current version.
	*/
	public function getVersion()
	{
		return '0.9.10';
	}
    
    public function getMenus() {
        return array(
            'authGroup' => array(
                'Title' => 'Authentication items',
                'Url' => '#',
                'Menus' => array(
                    'roles' => array('Title' => 'Roles (user groups)', 'Url' => '/Core/auth/default/roles'),
                    'pages' => array('Title' => 'Pages (actions)', 'Url' => '/Core/auth/default/operations'),
                    'tasks' => array('Title' => 'Custom Tasks', 'Url' => '/Core/auth/default/tasks'),
//                    'services' => array('Title' => 'Services', 'Url' => '/Core/auth/default/services'),
                )
            ),
            'permGroup' => array(
                'Title' => 'Manage permissions',
                'Url' => 'rights/default/permissions',
                'Menus' => array(
                    'pagePermissions' => array('Title' => 'Permissions', 'Url' => '/Core/auth/authItem/generate'),
                    'back' => array('Title' => '<br />Back to User list', 'Url' => '/Core/user/list'),
                )
            ),
        );
    }

}
